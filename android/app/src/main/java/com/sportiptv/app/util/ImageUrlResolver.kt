package com.sportiptv.app.util

object ImageUrlResolver {
    fun resolve(url: String?): String? {
        if (url.isNullOrBlank()) return null
        val trimmed = url.trim()
        if (trimmed.startsWith("http://") || trimmed.startsWith("https://")) {
            return trimmed
        }
        if (trimmed.startsWith("//")) {
            return "https:$trimmed"
        }
        val base = Constants.BASE_URL.removeSuffix("/api/").removeSuffix("/api")
        return when {
            trimmed.startsWith("/") -> "$base$trimmed"
            else -> "$base/$trimmed"
        }
    }
}
