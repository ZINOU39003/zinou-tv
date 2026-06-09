<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreChannelRequest;
use App\Http\Requests\Admin\UpdateChannelRequest;
use App\Models\Category;
use App\Models\Channel;
use App\Services\ChannelService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;

class ChannelController extends Controller
{
    protected ChannelService $channelService;

    public function __construct(ChannelService $channelService)
    {
        $this->channelService = $channelService;
    }

    public function index(Request $request): View
    {
        $search = $request->input('search');
        $categoryId = $request->input('category_id');
        $packageId = $request->input('package_id');

        $channels = Channel::when($search, function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('name_ar', 'like', "%{$search}%");
            })
            ->when($categoryId, function ($query) use ($categoryId) {
                $query->where('category_id', $categoryId);
            })
            ->when($packageId, function ($query) use ($packageId) {
                $query->where('package_id', $packageId);
            })
            ->with(['category', 'package'])
            ->orderBy('sort_order')
            ->paginate(15);

        $categories = Category::where('is_active', true)->orderBy('sort_order')->get();
        $packages = \App\Models\Package::where('is_active', true)
            ->when($categoryId, function ($query) use ($categoryId) {
                $query->where('category_id', $categoryId);
            })
            ->orderBy('sort_order')
            ->get();

        return view('admin.channels.index', compact('channels', 'categories', 'packages', 'search', 'categoryId', 'packageId'));
    }

    public function create(): View
    {
        $categories = Category::where('is_active', true)->orderBy('sort_order')->get();
        $packages = \App\Models\Package::where('is_active', true)->orderBy('sort_order')->get();
        return view('admin.channels.create', compact('categories', 'packages'));
    }

    public function store(StoreChannelRequest $request): RedirectResponse
    {
        $this->channelService->createChannel($request->validated());

        return redirect()->route('admin.channels.index')->with('success', 'Channel created successfully.');
    }

    public function edit(Channel $channel): View
    {
        $categories = Category::where('is_active', true)->orderBy('sort_order')->get();
        $packages = \App\Models\Package::where('is_active', true)->orderBy('sort_order')->get();
        
        // Decrypt URLs to show in form input fields
        $channel->decrypted_stream_url = $this->channelService->getDecryptedStreamUrl($channel);
        $channel->decrypted_backup_url = $this->channelService->getDecryptedBackupUrl($channel);

        // Decrypt server stream URLs
        foreach ($channel->servers as $server) {
            $server->decrypted_stream_url = resolve(\App\Services\EncryptionService::class)->decrypt($server->stream_url);
        }

        return view('admin.channels.edit', compact('channel', 'categories', 'packages'));
    }

    public function update(UpdateChannelRequest $request, Channel $channel): RedirectResponse
    {
        $this->channelService->updateChannel($channel, $request->validated());

        return redirect()->route('admin.channels.index')->with('success', 'Channel updated successfully.');
    }

    public function destroy(Channel $channel): RedirectResponse
    {
        $channel->delete();
        return redirect()->route('admin.channels.index')->with('success', 'تم حذف القناة بنجاح.');
    }

    public function destroyAll(): RedirectResponse
    {
        $count = Channel::count();
        // Delete channel servers first (foreign key), then channels
        \App\Models\ChannelServer::query()->delete();
        Channel::query()->delete();
        return redirect()->route('admin.channels.index')->with('success', "تم حذف جميع القنوات ({$count} قناة) بنجاح.");
    }

    public function reorder(Request $request): RedirectResponse
    {
        $orders = $request->input('orders', []);
        
        foreach ($orders as $id => $order) {
            Channel::where('id', $id)->update(['sort_order' => (int) $order]);
        }

        return redirect()->back()->with('success', 'Channel order updated.');
    }

    public function showImport(): View
    {
        $categories = Category::orderBy('sort_order')->get();
        return view('admin.channels.import', compact('categories'));
    }

    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'm3u_file' => 'nullable|file',
            'm3u_text' => 'nullable|string',
            'default_category_id' => 'required|exists:categories,id',
        ]);

        $m3uContent = '';
        if ($request->hasFile('m3u_file')) {
            $m3uContent = file_get_contents($request->file('m3u_file')->path());
        } elseif ($request->input('m3u_text')) {
            $m3uContent = $request->input('m3u_text');
        }

        if (empty(trim($m3uContent))) {
            return redirect()->back()->withErrors(['m3u_text' => 'Please provide M3U content or upload a file.'])->withInput();
        }

        $lines = explode("\n", $m3uContent);
        $channels = [];
        $currentChannel = null;

        // Fetch all categories for quick lookup and duplicate resolution
        $categories = Category::all();
        $categoryMap = [];
        foreach ($categories as $cat) {
            $categoryMap[$cat->slug] = $cat->id;
            $categoryMap[strtolower($cat->name)] = $cat->id;
            if ($cat->name_ar) {
                $categoryMap[strtolower($cat->name_ar)] = $cat->id;
            }
        }
        $defaultCategoryId = (int) $request->input('default_category_id');
        $autoCreateCategories = (bool) $request->input('auto_create_categories');
        $skipExisting = (bool) $request->input('skip_existing');

        // Fetch existing channel stream URLs and names for quick duplication check
        $existingUrls = [];
        if ($skipExisting) {
            $encryptionService = resolve(\App\Services\EncryptionService::class);
            $allChannels = Channel::all();
            foreach ($allChannels as $ch) {
                try {
                    $decrypted = $encryptionService->decrypt($ch->stream_url);
                    $existingUrls[strtolower($decrypted)] = true;
                } catch (\Exception $e) {}
                $existingUrls[strtolower($ch->name)] = true;
            }
        }

        $importCount = 0;
        $skipCount = 0;

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;

            if (str_starts_with($line, '#EXTINF:')) {
                $currentChannel = [];

                if (preg_match('/tvg-logo="([^"]+)"/', $line, $matches)) {
                    $currentChannel['logo_url'] = $matches[1];
                }

                if (preg_match('/group-title="([^"]+)"/', $line, $matches)) {
                    $currentChannel['group_title'] = trim($matches[1]);
                }

                $commaPos = strrpos($line, ',');
                if ($commaPos !== false) {
                    $currentChannel['name'] = trim(substr($line, $commaPos + 1));
                } else {
                    $currentChannel['name'] = 'Unknown Channel';
                }
            } elseif (str_starts_with($line, 'http://') || str_starts_with($line, 'https://')) {
                if ($currentChannel) {
                    $url = $line;
                    
                    // Duplicate check
                    if ($skipExisting && (isset($existingUrls[strtolower($url)]) || isset($existingUrls[strtolower($currentChannel['name'])]))) {
                        $skipCount++;
                        $currentChannel = null;
                        continue;
                    }

                    // Map category
                    $categoryId = $defaultCategoryId;
                    if (!empty($currentChannel['group_title'])) {
                        $groupName = $currentChannel['group_title'];
                        $slug = \Illuminate\Support\Str::slug($groupName);
                        if (empty($slug)) {
                            $slug = 'cat-' . time() . '-' . rand(1, 100);
                        }

                        $groupKey = strtolower($groupName);
                        if (isset($categoryMap[$slug])) {
                            $categoryId = $categoryMap[$slug];
                        } elseif (isset($categoryMap[$groupKey])) {
                            $categoryId = $categoryMap[$groupKey];
                        } elseif ($autoCreateCategories) {
                            // Determine Category Type
                            $catType = 'content_type';
                            $lowerGroup = strtolower($groupName);
                            if (str_contains($lowerGroup, 'sport') || str_contains($lowerGroup, 'event') || str_contains($lowerGroup, 'live')) {
                                $catType = 'content_type';
                            } elseif (str_contains($lowerGroup, 'movie') || str_contains($lowerGroup, 'cine') || str_contains($lowerGroup, 'show') || str_contains($lowerGroup, 'action') || str_contains($lowerGroup, 'drama') || str_contains($lowerGroup, 'film') || str_contains($lowerGroup, 'playz')) {
                                $catType = 'content_type';
                            } elseif (str_contains($lowerGroup, 'kid') || str_contains($lowerGroup, 'toon') || str_contains($lowerGroup, 'child') || str_contains($lowerGroup, 'junior') || str_contains($lowerGroup, 'smarty') || str_contains($lowerGroup, 'star')) {
                                $catType = 'content_type';
                            } elseif (str_contains($lowerGroup, 'religion') || str_contains($lowerGroup, 'islam') || str_contains($lowerGroup, 'quran') || str_contains($lowerGroup, 'deen')) {
                                $catType = 'content_type';
                            } elseif (str_contains($lowerGroup, 'bangla') || str_contains($lowerGroup, 'bd') || str_contains($lowerGroup, 'india') || str_contains($lowerGroup, 'saudi') || str_contains($lowerGroup, 'egypt') || str_contains($lowerGroup, 'usa') || str_contains($lowerGroup, 'uk') || str_contains($lowerGroup, 'arabic') || str_contains($lowerGroup, 'english') || str_contains($lowerGroup, 'akash') || str_contains($lowerGroup, 'international') || str_contains($lowerGroup, 'arab')) {
                                if (str_contains($lowerGroup, 'arabic') || str_contains($lowerGroup, 'english') || str_contains($lowerGroup, 'bangla') || str_contains($lowerGroup, 'hindi') || str_contains($lowerGroup, 'urdu')) {
                                    $catType = 'language';
                                } else {
                                    $catType = 'country';
                                }
                            }

                            // Create new category dynamically
                            $newCat = Category::create([
                                'name' => $groupName,
                                'name_ar' => $groupName,
                                'slug' => $slug,
                                'type' => $catType,
                                'is_active' => true,
                                'sort_order' => Category::max('sort_order') + 1,
                            ]);
                            // Update local lookup map
                            $categoryMap[$slug] = $newCat->id;
                            $categoryMap[$groupKey] = $newCat->id;
                            $categoryId = $newCat->id;
                        }
                    }

                    // Use smart classifier
                    $classifier = resolve(\App\Services\ChannelClassifierService::class);
                    $classification = $classifier->classify($currentChannel['name'], $currentChannel['group_title'] ?? null);

                    // Create the channel using ChannelService
                    $this->channelService->createChannel([
                        'name' => $currentChannel['name'],
                        'name_ar' => $currentChannel['name'],
                        'category_id' => $classification['category_id'] ?? $categoryId,
                        'logo_url' => $currentChannel['logo_url'] ?? null,
                        'stream_url' => $url,
                        'stream_type' => str_contains($url, '.mpd') ? 'mpd' : 'm3u8',
                        'quality' => 'HD',
                        'country' => $classification['country'] ?? null,
                        'language' => $classification['language'] ?? null,
                        'continent' => $classification['continent'] ?? null,
                        'is_active' => true,
                        'sort_order' => Channel::max('sort_order') + 1,
                    ]);

                    $importCount++;
                    $currentChannel = null;
                }
            }
        }

        $message = "Imported {$importCount} channels successfully.";
        if ($skipCount > 0) {
            $message .= " Skipped {$skipCount} existing channels.";
        }

        return redirect()->route('admin.channels.index')->with('success', $message);
    }
}
