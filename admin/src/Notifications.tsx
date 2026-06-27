import React, { useState } from 'react';

const Notifications: React.FC = () => {
  const [title, setTitle] = useState('');
  const [message, setMessage] = useState('');
  const [imageUrl, setImageUrl] = useState('');
  const [loading, setLoading] = useState(false);
  const [status, setStatus] = useState<{ type: 'success' | 'error'; text: string } | null>(null);

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setLoading(true);
    setStatus(null);

    try {
      const token = localStorage.getItem('zinou_token');
      const apiUrl = import.meta.env.VITE_API_URL || 'https://api.sportiptv.com';
      const response = await fetch(`${apiUrl}/api/admin/notifications/send`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${token}`,
          'X-API-Key': 'SportIptvDefaultApiKeySecret2026' // Based on backend setup
        },
        body: JSON.stringify({ title, message, image_url: imageUrl })
      });

      if (!response.ok) {
        throw new Error('فشل إرسال الإشعار. تحقق من الإعدادات.');
      }

      setStatus({ type: 'success', text: 'تم إرسال الإشعار بنجاح إلى جميع المستخدمين!' });
      setTitle('');
      setMessage('');
      setImageUrl('');
    } catch (err: any) {
      setStatus({ type: 'error', text: err.message || 'حدث خطأ غير متوقع.' });
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="p-6">
      <div className="flex justify-between items-center mb-6">
        <h1 className="text-2xl font-bold text-gray-100">إرسال إشعار مخصص</h1>
      </div>

      <div className="bg-gray-800 rounded-lg p-6 shadow-lg max-w-2xl">
        {status && (
          <div className={`p-4 mb-6 rounded-lg ${status.type === 'success' ? 'bg-green-900/50 text-green-400' : 'bg-red-900/50 text-red-400'}`}>
            {status.text}
          </div>
        )}

        <form onSubmit={handleSubmit} className="space-y-6">
          <div>
            <label className="block text-sm font-medium text-gray-400 mb-2">عنوان الإشعار</label>
            <input
              type="text"
              required
              value={title}
              onChange={(e) => setTitle(e.target.value)}
              placeholder="مثال: تحديث جديد للتطبيق!"
              className="w-full bg-gray-700 text-white border border-gray-600 rounded-lg px-4 py-2 focus:outline-none focus:border-blue-500"
            />
          </div>

          <div>
            <label className="block text-sm font-medium text-gray-400 mb-2">نص الإشعار</label>
            <textarea
              required
              rows={4}
              value={message}
              onChange={(e) => setMessage(e.target.value)}
              placeholder="اكتب رسالتك هنا..."
              className="w-full bg-gray-700 text-white border border-gray-600 rounded-lg px-4 py-2 focus:outline-none focus:border-blue-500"
            />
          </div>

          <div>
            <label className="block text-sm font-medium text-gray-400 mb-2">رابط الصورة (اختياري)</label>
            <input
              type="url"
              value={imageUrl}
              onChange={(e) => setImageUrl(e.target.value)}
              placeholder="https://example.com/image.jpg"
              className="w-full bg-gray-700 text-white border border-gray-600 rounded-lg px-4 py-2 focus:outline-none focus:border-blue-500"
            />
            <p className="text-xs text-gray-500 mt-1">إذا قمت بوضع رابط صورة، ستظهر بشكل كبير داخل الإشعار.</p>
          </div>

          <button
            type="submit"
            disabled={loading}
            className="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-lg transition-colors flex justify-center items-center"
          >
            {loading ? (
              <span className="animate-pulse">جاري الإرسال...</span>
            ) : (
              <span>إرسال الإشعار الآن 🚀</span>
            )}
          </button>
        </form>
      </div>
    </div>
  );
};

export default Notifications;
