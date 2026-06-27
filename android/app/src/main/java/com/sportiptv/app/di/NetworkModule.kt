package com.sportiptv.app.di

import com.sportiptv.app.data.local.prefs.SecurePreferences
import com.sportiptv.app.data.remote.api.AuthInterceptor
import com.sportiptv.app.data.remote.api.SportApi
import com.sportiptv.app.data.remote.api.TokenAuthenticator
import com.sportiptv.app.util.Constants
import dagger.Module
import dagger.Provides
import dagger.hilt.InstallIn
import dagger.hilt.components.SingletonComponent
import kotlinx.serialization.json.Json
import okhttp3.MediaType.Companion.toMediaType
import okhttp3.OkHttpClient
import okhttp3.logging.HttpLoggingInterceptor
import retrofit2.Retrofit
import retrofit2.converter.kotlinx.serialization.asConverterFactory
import java.util.concurrent.TimeUnit
import javax.inject.Singleton

@Module
@InstallIn(SingletonComponent::class)
object NetworkModule {

    @Provides
    @Singleton
    fun provideJson(): Json = Json {
        ignoreUnknownKeys = true
        coerceInputValues = true
        encodeDefaults = true
    }

    @Provides
    @Singleton
    fun provideAuthInterceptor(securePreferences: SecurePreferences): AuthInterceptor {
        return AuthInterceptor(securePreferences)
    }

    @Provides
    @Singleton
    fun provideTokenAuthenticator(
        securePreferences: SecurePreferences,
        json: Json
    ): TokenAuthenticator {
        return TokenAuthenticator(securePreferences, json)
    }

    @Provides
    @Singleton
    fun provideOkHttpClient(
        authInterceptor: AuthInterceptor,
        tokenAuthenticator: TokenAuthenticator,
        telemetryInterceptor: com.sportiptv.app.data.remote.api.TelemetryInterceptor
    ): OkHttpClient {
        val loggingInterceptor = HttpLoggingInterceptor().apply {
            level = HttpLoggingInterceptor.Level.HEADERS
        }

        // Trust all certificates for localtunnel/ngrok SSL validation bypass
        val trustAllCerts = arrayOf<javax.net.ssl.TrustManager>(
            object : javax.net.ssl.X509TrustManager {
                override fun checkClientTrusted(chain: Array<out java.security.cert.X509Certificate>?, authType: String?) {}
                override fun checkServerTrusted(chain: Array<out java.security.cert.X509Certificate>?, authType: String?) {}
                override fun getAcceptedIssuers(): Array<java.security.cert.X509Certificate> = arrayOf()
            }
        )

        val sslContext = javax.net.ssl.SSLContext.getInstance("SSL").apply {
            init(null, trustAllCerts, java.security.SecureRandom())
        }

        return OkHttpClient.Builder()
            .connectTimeout(60, TimeUnit.SECONDS)
            .readTimeout(60, TimeUnit.SECONDS)
            .writeTimeout(60, TimeUnit.SECONDS)
            .sslSocketFactory(sslContext.socketFactory, trustAllCerts[0] as javax.net.ssl.X509TrustManager)
            .hostnameVerifier { _, _ -> true }
            .addInterceptor(authInterceptor)
            .addInterceptor(telemetryInterceptor)
            .addInterceptor(loggingInterceptor)
            .authenticator(tokenAuthenticator)
            .build()
    }

    @Provides
    @Singleton
    fun provideRetrofit(okHttpClient: OkHttpClient, json: Json): Retrofit {
        val contentType = "application/json".toMediaType()
        return Retrofit.Builder()
            .baseUrl(Constants.BASE_URL)
            .client(okHttpClient)
            .addConverterFactory(json.asConverterFactory(contentType))
            .build()
    }

    @Provides
    @Singleton
    fun provideSportApi(retrofit: Retrofit): SportApi {
        return retrofit.create(SportApi::class.java)
    }
}
