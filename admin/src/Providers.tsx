import { useState, useEffect } from 'react';

export default function Providers() {
  const [providers, setProviders] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  const [showAdd, setShowAdd] = useState(false);
  const [formData, setFormData] = useState({ name: '', type: 'xtream', url: '', username: '', password: '' });

  const fetchProviders = async () => {
    try {
      const baseUrl = import.meta.env.VITE_API_URL || 'https://api.zinou-tv.workers.dev';
      const res = await fetch(`${baseUrl}/api/providers`, {
        headers: { 'Authorization': `Bearer ${localStorage.getItem('zinou_token')}` }
      });
      const data = await res.json();
      if (Array.isArray(data)) {
        setProviders(data);
      } else {
        console.error('Expected array but got:', data);
        setProviders([]);
      }
    } catch (err) {
      console.error(err);
      setProviders([]);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchProviders();
  }, []);

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    try {
      const baseUrl = import.meta.env.VITE_API_URL || 'https://api.zinou-tv.workers.dev';
      const res = await fetch(`${baseUrl}/api/providers`, {
        method: 'POST',
        headers: { 
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${localStorage.getItem('zinou_token')}`
        },
        body: JSON.stringify(formData)
      });
      
      if (!res.ok) {
        const errorData = await res.json();
        alert('Error: ' + JSON.stringify(errorData));
        return;
      }
      
      setShowAdd(false);
      setFormData({ name: '', type: 'xtream', url: '', username: '', password: '' });
      fetchProviders();
    } catch (err) {
      alert('Failed to add provider: ' + err);
    }
  };

  const handleDelete = async (id: number) => {
    if (!confirm('Are you sure?')) return;
    try {
      const baseUrl = import.meta.env.VITE_API_URL || 'https://api.zinou-tv.workers.dev';
      await fetch(`${baseUrl}/api/providers/${id}`, {
        method: 'DELETE',
        headers: { 'Authorization': `Bearer ${localStorage.getItem('zinou_token')}` }
      });
      fetchProviders();
    } catch (err) {
      console.error(err);
    }
  };

  const handleSync = async (id: number) => {
    const p = providers.find(x => x.id === id);
    if (!p) return;

    if (!confirm('This may take a few minutes for large providers. Continue?')) return;
    
    // Fallback to server sync for m3u for now
    if (p.type === 'm3u') {
      try {
        const baseUrl = import.meta.env.VITE_API_URL || 'https://api.zinou-tv.workers.dev';
        const res = await fetch(`${baseUrl}/api/providers/${id}/sync`, {
          method: 'POST',
          headers: { 'Authorization': `Bearer ${localStorage.getItem('zinou_token')}` }
        });
        const data = await res.json();
        if (data.success) {
          alert('Sync completed successfully!');
          fetchProviders();
        } else {
          alert('Sync failed: ' + data.error);
        }
      } catch (err) {
        console.error(err);
        alert('Sync failed. Check console.');
      }
      return;
    }

    // Client-side Sync for Xtream
    try {
      const baseUrl = import.meta.env.VITE_API_URL || 'https://api.zinou-tv.workers.dev';
      const token = localStorage.getItem('zinou_token');
      
      alert('Starting Client-Side Sync! Please do not close the window...');
      
      const cleanUrl = p.url.trim().replace(/\/$/, '');
      const user = p.username?.trim();
      const pass = p.password?.trim();
      const baseXtream = `${cleanUrl}/player_api.php?username=${user}&password=${pass}`;

      // 1. Initialize server (Clean old data)
      const initRes = await fetch(`${baseUrl}/api/providers/${id}/sync-client/init`, {
        method: 'POST',
        headers: { 'Authorization': `Bearer ${token}` }
      });
      if (!initRes.ok) throw new Error('Failed to initialize sync on server');

      // 1. Fetch Live Categories
      const liveCatRes = await fetch(`${baseXtream}&action=get_live_categories`);
      const liveCats = await liveCatRes.json();
      if (!Array.isArray(liveCats)) {
        throw new Error('XTREAM API did not return an array for live categories. It might be rate limiting you. Please wait a few minutes and try again. Response: ' + JSON.stringify(liveCats).substring(0, 100));
      }
      
      // 3. Fetch VOD Categories
      const vodCatRes = await fetch(`${baseXtream}&action=get_vod_categories`);
      const vodCats = await vodCatRes.json();

      const allCats = [];
      if (Array.isArray(liveCats)) liveCats.forEach(c => allCats.push({ providerId: id, externalId: c.category_id, name: c.category_name, type: 'live' }));
      if (Array.isArray(vodCats)) vodCats.forEach(c => allCats.push({ providerId: id, externalId: c.category_id, name: c.category_name, type: 'movie' }));

      // Send Categories
      await fetch(`${baseUrl}/api/providers/${id}/sync-client/chunk`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Authorization': `Bearer ${token}` },
        body: JSON.stringify({ categories: allCats })
      });

      // 4. Fetch Live Streams
      const liveRes = await fetch(`${baseXtream}&action=get_live_streams`);
      const liveStreams = await liveRes.json();
      if (!Array.isArray(liveStreams)) {
        throw new Error('XTREAM API did not return an array for live streams. Please wait a few minutes. Response: ' + JSON.stringify(liveStreams).substring(0, 100));
      }
      
      const formattedChannels = [];
      if (Array.isArray(liveStreams)) {
        liveStreams.forEach((s: any) => {
          formattedChannels.push({
            providerId: id,
            externalCategoryId: s.category_id?.toString() || '0',
            name: s.name || 'Unknown',
            streamId: s.stream_id?.toString() || '0',
            streamIcon: s.stream_icon || '',
            epgChannelId: s.epg_channel_id || '',
            streamBaseUrl: url,
            username: user,
            password: pass
          });
        });
      }

      // Chunk and send channels (50 at a time to prevent Cloudflare Worker subrequest limits)
      const CHUNK_SIZE = 50;
      for (let i = 0; i < formattedChannels.length; i += CHUNK_SIZE) {
        const chunk = formattedChannels.slice(i, i + CHUNK_SIZE);
        await fetch(`${baseUrl}/api/providers/${id}/sync-client/chunk`, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', 'Authorization': `Bearer ${token}` },
          body: JSON.stringify({ channels: chunk })
        });
      }

      // 5. Fetch VOD Streams
      const vodRes = await fetch(`${baseXtream}&action=get_vod_streams`);
      const vodStreams = await vodRes.json();
      if (!Array.isArray(vodStreams)) {
         throw new Error('XTREAM API did not return an array for VOD streams.');
      }
      
      const formattedMovies = [];
      if (Array.isArray(vodStreams)) {
        vodStreams.forEach((m: any) => {
          formattedMovies.push({
            providerId: id,
            externalCategoryId: m.category_id?.toString() || '0',
            name: m.name || 'Unknown',
            streamId: m.stream_id?.toString() || '0',
            streamIcon: m.stream_icon || '',
            rating: m.rating || '',
            added: m.added || '',
            container_extension: m.container_extension || 'mp4',
            streamBaseUrl: url,
            username: user,
            password: pass
          });
        });
      }

      // Chunk and send movies
      for (let i = 0; i < formattedMovies.length; i += CHUNK_SIZE) {
        const chunk = formattedMovies.slice(i, i + CHUNK_SIZE);
        const isLast = (i + CHUNK_SIZE) >= formattedMovies.length;
        await fetch(`${baseUrl}/api/providers/${id}/sync-client/chunk`, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', 'Authorization': `Bearer ${token}` },
          body: JSON.stringify({ movies: chunk, isLast })
        });
      }

      alert(`Sync completed! Synced ${allCats.length} categories, ${formattedChannels.length} live channels, and ${formattedMovies.length} movies.`);
      fetchProviders();

    } catch (err: any) {
      console.error(err);
      alert('Client-side sync failed: ' + err.message);
    }
  };

  if (loading) return <div className="text-gray-400">Loading providers...</div>;

  return (
    <div>
      <div className="flex justify-between items-center mb-6">
        <h3 className="text-xl font-bold">IPTV Providers</h3>
        <button 
          onClick={() => setShowAdd(!showAdd)}
          className="bg-cyan-500/20 text-cyan-400 hover:bg-cyan-500/30 px-4 py-2 rounded-lg font-bold text-sm transition-all"
        >
          {showAdd ? 'Cancel' : '+ Add Provider'}
        </button>
      </div>

      {showAdd && (
        <form onSubmit={handleSubmit} className="bg-gray-800/50 p-6 rounded-2xl border border-gray-700/50 mb-8 space-y-4">
          <div className="grid grid-cols-2 gap-4">
            <div>
              <label className="block text-sm text-gray-400 mb-1">Name</label>
              <input type="text" required value={formData.name} onChange={e => setFormData({...formData, name: e.target.value})} className="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2 text-white" />
            </div>
            <div>
              <label className="block text-sm text-gray-400 mb-1">Type</label>
              <select value={formData.type} onChange={e => setFormData({...formData, type: e.target.value})} className="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2 text-white">
                <option value="xtream">Xtream Codes</option>
                <option value="m3u">M3U Playlist</option>
              </select>
            </div>
            <div>
              <label className="block text-sm text-gray-400 mb-1">Server URL</label>
              <input type="url" required value={formData.url} onChange={e => setFormData({...formData, url: e.target.value})} className="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2 text-white" placeholder="http://example.com:8080" />
            </div>
            <div>
              <label className="block text-sm text-gray-400 mb-1">Username</label>
              <input type="text" value={formData.username} onChange={e => setFormData({...formData, username: e.target.value})} className="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2 text-white" />
            </div>
            <div>
              <label className="block text-sm text-gray-400 mb-1">Password</label>
              <input type="text" value={formData.password} onChange={e => setFormData({...formData, password: e.target.value})} className="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2 text-white" />
            </div>
          </div>
          <button type="submit" className="bg-cyan-500 text-white font-bold px-6 py-2 rounded-lg hover:bg-cyan-400">Save Provider</button>
        </form>
      )}

      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        {providers.map(p => (
          <div key={p.id} className="bg-gray-800/40 p-6 rounded-2xl border border-gray-700/50 relative group">
            <div className="flex justify-between items-start mb-4">
              <h4 className="font-bold text-lg">{p.name}</h4>
              <span className="text-xs bg-cyan-500/10 text-cyan-400 px-2 py-1 rounded-md uppercase font-bold">{p.type}</span>
            </div>
            <p className="text-sm text-gray-400 mb-4 truncate">{p.url}</p>
            <div className="flex justify-between items-center text-xs text-gray-500">
              <div className="flex items-center gap-2">
                <span>{p.isActive ? '🟢 Active' : '🔴 Inactive'}</span>
                {p.lastSyncAt && <span className="text-gray-600">| Synced: {new Date(p.lastSyncAt).toLocaleDateString()}</span>}
              </div>
              <div className="flex items-center gap-3 opacity-0 group-hover:opacity-100 transition-opacity">
                <button onClick={() => handleSync(p.id)} className="text-cyan-400 hover:text-cyan-300">Sync Now</button>
                <button onClick={() => handleDelete(p.id)} className="text-red-400 hover:text-red-300">Delete</button>
              </div>
            </div>
          </div>
        ))}
        {providers.length === 0 && !showAdd && (
          <p className="text-gray-500 col-span-3 text-center py-10">No providers found. Add one to sync channels!</p>
        )}
      </div>
    </div>
  );
}
