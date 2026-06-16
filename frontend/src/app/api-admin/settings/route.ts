import { NextRequest, NextResponse } from 'next/server';
import fs from 'fs';
import path from 'path';

const SETTINGS_PATH = path.join(process.cwd(), 'src/data/download-settings.json');
const DEFAULT_PASSCODE = process.env.ADMIN_PASSCODE || 'admin123';

function checkAuth(req: NextRequest): boolean {
  const passcode = req.headers.get('x-admin-passcode') || req.nextUrl.searchParams.get('passcode');
  return passcode === DEFAULT_PASSCODE;
}

export const dynamic = 'force-dynamic';

export async function GET(req: NextRequest) {
  try {
    if (!fs.existsSync(SETTINGS_PATH)) {
      return NextResponse.json({ error: 'Settings file not found' }, { status: 404 });
    }
    const fileContent = fs.readFileSync(SETTINGS_PATH, 'utf-8');
    const settings = JSON.parse(fileContent);
    return NextResponse.json(settings, {
      headers: {
        'Cache-Control': 'no-store, no-cache, must-revalidate, proxy-revalidate',
        'Pragma': 'no-cache',
        'Expires': '0',
      }
    });
  } catch (error: any) {
    return NextResponse.json({ error: 'Failed to read settings: ' + error.message }, { status: 500 });
  }
}

export async function POST(req: NextRequest) {
  try {
    if (!checkAuth(req)) {
      return NextResponse.json({ error: 'غير مصرح بالدخول (Unauthorized)' }, { status: 401 });
    }

    const body = await req.json();
    
    // Minimal validation to make sure structure is kept
    if (!body.hero || !body.features || !body.pricing || !body.faqs || !body.activationModal) {
      return NextResponse.json({ error: 'بنية البيانات غير صالحة (Invalid settings structure)' }, { status: 400 });
    }

    // Write back to settings JSON
    fs.writeFileSync(SETTINGS_PATH, JSON.stringify(body, null, 2), 'utf-8');
    
    return NextResponse.json({ success: true, message: 'تم حفظ الإعدادات بنجاح!' });
  } catch (error: any) {
    return NextResponse.json({ error: 'Failed to write settings: ' + error.message }, { status: 500 });
  }
}
