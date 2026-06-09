# R8 Obfuscation Rules for Sport IPTV App

# General Kotlin and Serialization rules
-keepattributes Signature, *Annotation*, EnclosingMethod, InnerClasses, SourceFile, LineNumberTable
-renamesourcefileattribute SourceFile

-if @kotlinx.serialization.Serializable class **
-keepclassmembers class <1> {
    static <1>$Companion Companion;
}
-if @kotlinx.serialization.Serializable class ** {
    static **$* *;
}
-keepclassmembers class <2>$<3> {
    kotlinx.serialization.KSerializer serializer(...);
}
-if @kotlinx.serialization.Serializable class ** {
    public static ** INSTANCE;
}
-keepclassmembers class <1> {
    public static ** INSTANCE;
    kotlinx.serialization.KSerializer serializer(...);
}

# OkHttp rules
-dontwarn okhttp3.internal.platform.**
-dontwarn org.conscrypt.**
-dontwarn org.bouncycastle.**
-dontwarn org.openjsse.**

# Retrofit rules
-keepclassmembers class * {
    @retrofit2.http.* <methods>;
}

# Room rules (handled mostly by consumer rules, but good to ensure models are kept)
-keep class * extends androidx.room.RoomDatabase

# Keep our Domain models and DTOs from obfuscation of properties names (if needed for serialization)
-keepclassmembers class com.sportiptv.app.data.remote.dto.** { <fields>; }
-keepclassmembers class com.sportiptv.app.domain.model.** { <fields>; }
