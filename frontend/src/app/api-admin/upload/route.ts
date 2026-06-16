import { NextRequest, NextResponse } from 'next/server';
import fs from 'fs';
import path from 'path';

const DEFAULT_PASSCODE = process.env.ADMIN_PASSCODE || 'admin123';

function checkAuth(req: NextRequest): boolean {
  const rawPasscode = req.headers.get('x-admin-passcode') || req.nextUrl.searchParams.get('passcode') || '';
  try {
    const passcode = decodeURIComponent(rawPasscode);
    return passcode === DEFAULT_PASSCODE;
  } catch {
    return rawPasscode === DEFAULT_PASSCODE;
  }
}

export async function POST(req: NextRequest) {
  try {
    if (!checkAuth(req)) {
      return NextResponse.json({ error: 'غير مصرح بالعملية (Unauthorized)' }, { status: 401 });
    }

    const formData = await req.formData();
    const file = formData.get('file') as File | null;
    const type = formData.get('type') as string | null; // 'apk' or 'image'

    if (!file) {
      return NextResponse.json({ error: 'لم يتم رفع أي ملف' }, { status: 400 });
    }

    const bytes = await file.arrayBuffer();
    const buffer = Buffer.from(bytes);

    if (type === 'apk') {
      // Overwrite the visitor apk directly in the public root folder
      const apkPath = path.join(process.cwd(), 'public', 'zinou-tv.apk');
      fs.writeFileSync(apkPath, buffer);
      return NextResponse.json({ 
        success: true, 
        url: '/zinou-tv.apk',
        message: 'تم تحديث ملف التطبيق (APK) بنجاح!' 
      });
    } else {
      // Save custom image to public/uploads/
      const uploadsDir = path.join(process.cwd(), 'public', 'uploads');
      if (!fs.existsSync(uploadsDir)) {
        fs.mkdirSync(uploadsDir, { recursive: true });
      }

      // Sanitize filename and append timestamp
      const originalName = file.name.replace(/[^a-zA-Z0-9.-]/g, '_');
      const filename = `${Date.now()}_${originalName}`;
      const filePath = path.join(uploadsDir, filename);
      
      fs.writeFileSync(filePath, buffer);

      return NextResponse.json({
        success: true,
        url: `/uploads/${filename}`,
        message: 'تم رفع الصورة بنجاح!'
      });
    }
  } catch (error: any) {
    return NextResponse.json({ error: 'فشل في رفع الملف: ' + error.message }, { status: 500 });
  }
}
