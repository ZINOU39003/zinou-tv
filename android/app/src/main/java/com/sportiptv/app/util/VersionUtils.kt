package com.sportiptv.app.util

object VersionUtils {
    fun isLowerThan(current: String, minimum: String): Boolean {
        val currentParts = parse(current)
        val minimumParts = parse(minimum)
        val maxLen = maxOf(currentParts.size, minimumParts.size)
        for (i in 0 until maxLen) {
            val c = currentParts.getOrElse(i) { 0 }
            val m = minimumParts.getOrElse(i) { 0 }
            if (c < m) return true
            if (c > m) return false
        }
        return false
    }

    private fun parse(version: String): List<Int> =
        version.split(".", "-", "_")
            .mapNotNull { it.filter(Char::isDigit).toIntOrNull() }
            .ifEmpty { listOf(0) }
}
