import { Hono } from 'hono';

export const notificationsRoutes = new Hono<{ Bindings: { ONESIGNAL_APP_ID: string, ONESIGNAL_REST_API_KEY: string } }>();

notificationsRoutes.post('/send', async (c) => {
  const body = await c.req.json();
  const { title, message, image_url } = body;

  if (!title || !message) {
    return c.json({ error: 'Title and message are required' }, 400);
  }

  // Use the same keys as the VPS, from env
  const appId = c.env.ONESIGNAL_APP_ID;
  const apiKey = c.env.ONESIGNAL_REST_API_KEY;

  if (!appId || !apiKey) {
    return c.json({ error: 'OneSignal credentials not configured in Cloudflare environment' }, 500);
  }

  const payload: any = {
    app_id: appId,
    included_segments: ['All'],
    headings: { en: title, ar: title },
    contents: { en: message, ar: message },
  };

  if (image_url) {
    payload.big_picture = image_url;
    payload.ios_attachments = { id1: image_url };
  }

  try {
    const response = await fetch('https://onesignal.com/api/v1/notifications', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json; charset=utf-8',
        'Authorization': `Basic ${apiKey}`,
      },
      body: JSON.stringify(payload),
    });

    if (response.ok) {
      return c.json({ success: true, message: 'Notification sent successfully' });
    } else {
      const errorData = await response.text();
      return c.json({ error: 'Failed to send notification to OneSignal', details: errorData }, 500);
    }
  } catch (error: any) {
    return c.json({ error: error.message }, 500);
  }
});
