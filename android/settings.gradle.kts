pluginManagement {
    repositories {
        google {
            content {
                includeGroupByRegex("com\\.android.*")
                includeGroupByRegex("com\\.google.*")
                includeGroupByRegex("androidx.*")
            }
        }
        mavenCentral()
        gradlePluginPortal()
    }
}
plugins {
    id("org.gradle.toolchains.foojay-resolver-convention") version "1.0.0"
}
dependencyResolutionManagement {
    repositoriesMode.set(RepositoriesMode.FAIL_ON_PROJECT_REPOS)
    repositories {
        google()
        mavenCentral()
        // For freeRASP if needed in production
        maven { url = java.net.URI("https://europe-west3-maven.pkg.dev/talsec-artifact-repository/freerasp") }
        // For Xposed API
        maven { url = java.net.URI("https://api.xposed.info/") }
    }
}

rootProject.name = "Sport IPTV"
include(":app")
include(":stream-detector:app")
include(":xposed-detector")
