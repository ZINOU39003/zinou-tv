<?php

namespace App\Enums;

enum StreamType: string
{
    case M3U8 = 'm3u8';
    case MPD = 'mpd';
    case TS = 'ts';
}
