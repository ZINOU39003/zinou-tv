package com.sportiptv.app.data.local.prefs

import android.content.Context
import androidx.datastore.core.DataStore
import androidx.datastore.preferences.core.Preferences
import androidx.datastore.preferences.core.booleanPreferencesKey
import androidx.datastore.preferences.core.edit
import androidx.datastore.preferences.core.stringPreferencesKey
import androidx.datastore.preferences.preferencesDataStore
import kotlinx.coroutines.flow.Flow
import kotlinx.coroutines.flow.map

private val Context.dataStore: DataStore<Preferences> by preferencesDataStore(name = "user_preferences")

class UserPreferences(private val context: Context) {

    companion object {
        private val KEY_THEME_MODE = stringPreferencesKey("theme_mode")
        private val KEY_LANGUAGE = stringPreferencesKey("language")
        private val KEY_AUTO_PLAY = booleanPreferencesKey("auto_play")
        private val KEY_NOTIFICATIONS_ENABLED = booleanPreferencesKey("notifications_enabled")
        private val KEY_PREFERRED_QUALITY = stringPreferencesKey("preferred_quality")
        private val KEY_LAST_CATEGORY_ID = stringPreferencesKey("last_category_id")
    }

    val themeMode: Flow<String> = context.dataStore.data.map { preferences ->
        preferences[KEY_THEME_MODE] ?: "dark"
    }

    val language: Flow<String> = context.dataStore.data.map { preferences ->
        preferences[KEY_LANGUAGE] ?: "en"
    }

    val autoPlay: Flow<Boolean> = context.dataStore.data.map { preferences ->
        preferences[KEY_AUTO_PLAY] ?: true
    }

    val notificationsEnabled: Flow<Boolean> = context.dataStore.data.map { preferences ->
        preferences[KEY_NOTIFICATIONS_ENABLED] ?: true
    }

    val preferredQuality: Flow<String> = context.dataStore.data.map { preferences ->
        preferences[KEY_PREFERRED_QUALITY] ?: "auto"
    }

    val lastCategoryId: Flow<String> = context.dataStore.data.map { preferences ->
        preferences[KEY_LAST_CATEGORY_ID] ?: ""
    }

    suspend fun setThemeMode(mode: String) {
        context.dataStore.edit { preferences ->
            preferences[KEY_THEME_MODE] = mode
        }
    }

    suspend fun setLanguage(language: String) {
        context.dataStore.edit { preferences ->
            preferences[KEY_LANGUAGE] = language
        }
    }

    suspend fun setAutoPlay(enabled: Boolean) {
        context.dataStore.edit { preferences ->
            preferences[KEY_AUTO_PLAY] = enabled
        }
    }

    suspend fun setNotificationsEnabled(enabled: Boolean) {
        context.dataStore.edit { preferences ->
            preferences[KEY_NOTIFICATIONS_ENABLED] = enabled
        }
    }

    suspend fun setPreferredQuality(quality: String) {
        context.dataStore.edit { preferences ->
            preferences[KEY_PREFERRED_QUALITY] = quality
        }
    }

    suspend fun setLastCategoryId(categoryId: String) {
        context.dataStore.edit { preferences ->
            preferences[KEY_LAST_CATEGORY_ID] = categoryId
        }
    }

    suspend fun clearAll() {
        context.dataStore.edit { it.clear() }
    }
}
