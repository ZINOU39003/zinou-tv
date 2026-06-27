import { Hono } from 'hono';
import { cors } from 'hono/cors';
import { getDb } from './db';
import { users } from './db/schema';

type Bindings = {
  DATABASE_URL: string;
  JWT_SECRET: string;
};

const app = new Hono<{ Bindings: Bindings }>();

import appRoutes from './routes/app';
import scoresRoutes from './routes/scores';

app.onError((err, c) => {
  console.error(err);
  return c.json({ error: err.message, stack: err.stack, name: err.name, url: c.env.DATABASE_URL }, 500);
});

app.use('*', cors());

// Mount the new App & Proxy routes
app.route('/api/app', appRoutes);
app.route('/api/app/scores', scoresRoutes);

app.get('/', (c) => {
  return c.json({ message: 'Zinou TV API Serverless is running!' });
});

app.get('/api/health', async (c) => {
  try {
    const db = getDb(c.env.DATABASE_URL);
    // Simple query to verify db connection
    const result = await db.select().from(users).limit(1);
    return c.json({ status: 'ok', db: 'connected' });
  } catch (error) {
    console.error(error);
    return c.json({ status: 'error', db: 'disconnected' }, 500);
  }
});

import { authRoutes } from './routes/auth';
import { providersRoutes } from './routes/providers';
import { channelsRoutes } from './routes/channels';
import { usersRoutes } from './routes/users';
import { settingsRoutes } from './routes/settings';
import { moviesRoutes } from './routes/movies';
import { packagesRoutes } from './routes/packages';
import { activationRoutes } from './routes/activation';
import { notificationsRoutes } from './routes/notifications';
import { runSyncJob } from './sync';

app.route('/api/auth', authRoutes);
app.route('/api/providers', providersRoutes);
app.route('/api/channels', channelsRoutes);
app.route('/api/users', usersRoutes);
app.route('/api/settings', settingsRoutes);
app.route('/api/movies', moviesRoutes);
app.route('/api/packages', packagesRoutes);
app.route('/api/activation', activationRoutes);
app.route('/api/admin/notifications', notificationsRoutes);

import { getDb } from './db';
import { channels, providers, users, deviceSessions } from './db/schema';
import { count, gte } from 'drizzle-orm';

app.get('/api/dashboard', async (c) => {
  const db = getDb(c.env.DATABASE_URL);
  const totalChannels = await db.select({ value: count() }).from(channels);
  const totalProviders = await db.select({ value: count() }).from(providers);
  const totalUsers = await db.select({ value: count() }).from(users);
  const totalInstalls = await db.select({ value: count() }).from(deviceSessions);
  
  const today = new Date();
  today.setHours(0, 0, 0, 0);
  const activeUsersToday = await db.select({ value: count() }).from(deviceSessions).where(gte(deviceSessions.lastActiveAt, today));
  
  return c.json({
    channels: totalChannels[0].value,
    providers: totalProviders[0].value,
    users: totalUsers[0].value,
    installs: totalInstalls[0].value,
    active_users: activeUsersToday[0].value
  });
});

export default {
  fetch: app.fetch,
  scheduled: async (event: any, env: any, ctx: any) => {
    ctx.waitUntil(runSyncJob(env));
  }
};
