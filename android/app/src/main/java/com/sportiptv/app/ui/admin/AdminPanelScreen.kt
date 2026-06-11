package com.sportiptv.app.ui.admin

import android.app.Activity
import android.net.Uri
import android.view.ViewGroup
import android.webkit.ValueCallback
import android.webkit.WebChromeClient
import android.webkit.WebSettings
import android.webkit.WebView
import android.webkit.WebViewClient
import androidx.activity.compose.BackHandler
import androidx.activity.compose.rememberLauncherForActivityResult
import androidx.activity.result.contract.ActivityResultContracts
import androidx.compose.foundation.background
import androidx.compose.foundation.layout.*
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.ArrowBack
import androidx.compose.material.icons.filled.Image
import androidx.compose.material.icons.filled.CloudUpload
import androidx.compose.material.icons.filled.Refresh
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.platform.LocalContext
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import androidx.compose.ui.viewinterop.AndroidView
import com.sportiptv.app.ui.theme.BgPrimary
import com.sportiptv.app.ui.theme.Primary
import com.sportiptv.app.util.Constants
import android.widget.Toast
import androidx.compose.runtime.rememberCoroutineScope
import kotlinx.coroutines.CoroutineScope
import kotlinx.coroutines.launch
import android.content.Context
import okhttp3.MediaType.Companion.toMediaTypeOrNull
import okhttp3.RequestBody.Companion.toRequestBody

@Composable
fun AdminPanelScreen(
    onBackClick: () -> Unit
) {
    val context = LocalContext.current
    val scope = rememberCoroutineScope()
    var webViewInstance by remember { mutableStateOf<WebView?>(null) }
    var loadingProgress by remember { mutableStateOf(0) }
    var isPageLoading by remember { mutableStateOf(true) }

    // File Upload Callback handling
    var filePathCallback by remember { mutableStateOf<ValueCallback<Array<Uri>>?>(null) }

    val fileChooserLauncher = rememberLauncherForActivityResult(
        contract = ActivityResultContracts.StartActivityForResult()
    ) { result ->
        if (result.resultCode == Activity.RESULT_OK) {
            val data = result.data
            val results = WebChromeClient.FileChooserParams.parseResult(result.resultCode, data)
            filePathCallback?.onReceiveValue(results)
        } else {
            filePathCallback?.onReceiveValue(null)
        }
        filePathCallback = null
    }

    // Image picker for uploading logos/icons from device
    val imagePickerLauncher = rememberLauncherForActivityResult(
        contract = ActivityResultContracts.GetContent()
    ) { uri: Uri? ->
        uri?.let { selectedUri ->
            scope.launch {
                val success = uploadImage(selectedUri, context)
                if (success) {
                    Toast.makeText(context, "✅ تم رفع الصورة بنجاح", Toast.LENGTH_SHORT).show()
                } else {
                    Toast.makeText(context, "⚠️ فشل رفع الصورة", Toast.LENGTH_SHORT).show()
                }
            }
        }
    }

    // Intercept back key to navigate backward in web view history if possible
    BackHandler(enabled = webViewInstance?.canGoBack() == true) {
        webViewInstance?.goBack()
    }

    // Construct the Admin Panel URL
    val adminUrl = remember {
        val base = Constants.BASE_URL.removeSuffix("/api/").removeSuffix("/api")
        "$base/admin"
    }

    Column(
        modifier = Modifier
            .fillMaxSize()
            .background(BgPrimary)
    ) {
        // Top Bar
        Row(
            modifier = Modifier
                .fillMaxWidth()
                .padding(horizontal = 16.dp, vertical = 12.dp),
            verticalAlignment = Alignment.CenterVertically
        ) {
            IconButton(onClick = onBackClick) {
                Icon(
                    imageVector = Icons.Default.ArrowBack,
                    contentDescription = "Back",
                    tint = Color.White
                )
            }
            // Upload logo/icon button
            IconButton(onClick = { imagePickerLauncher.launch("image/*") }) {
                Icon(
                    imageVector = Icons.Default.CloudUpload,
                    contentDescription = "Upload Image",
                    tint = Color.White
                )
            }

            Spacer(modifier = Modifier.width(8.dp))

            Text(
                text = "لوحة الإدارة ZINOU TV",
                color = Color.White,
                fontSize = 18.sp,
                fontWeight = FontWeight.Bold,
                modifier = Modifier.weight(1f)
            )

            IconButton(onClick = { webViewInstance?.reload() }) {
                Icon(
                    imageVector = Icons.Default.Refresh,
                    contentDescription = "Refresh",
                    tint = Color.White
                )
            }
        }

        // Loading Linear Progress Bar
        if (isPageLoading) {
            LinearProgressIndicator(
                progress = { loadingProgress / 100f },
                modifier = Modifier
                    .fillMaxWidth()
                    .height(3.dp),
                color = Primary,
                trackColor = Color.Transparent
            )
        } else {
            Spacer(modifier = Modifier.height(3.dp))
        }

        // WebView Container
        Box(
            modifier = Modifier
                .fillMaxWidth()
                .weight(1f)
        ) {
            AndroidView(
                factory = { ctx ->
                    WebView(ctx).apply {
                        layoutParams = ViewGroup.LayoutParams(
                            ViewGroup.LayoutParams.MATCH_PARENT,
                            ViewGroup.LayoutParams.MATCH_PARENT
                        )

                        // Settings configuration
                        settings.apply {
                            javaScriptEnabled = true
                            domStorageEnabled = true
                            databaseEnabled = true
                            cacheMode = WebSettings.LOAD_DEFAULT

                            // Enable viewport configurations for responsiveness
                            useWideViewPort = true
                            loadWithOverviewMode = true

                            // Set mobile-friendly User-Agent
                            userAgentString = "Mozilla/5.0 (Linux; Android 10; Mobile) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/118.0.0.0 Mobile Safari/537.36"

                            // Zoom configurations
                            builtInZoomControls = true
                            displayZoomControls = false
                            setSupportZoom(true)

                            allowFileAccess = true
                        }

                        // Handle page navigation internally
                        webViewClient = object : WebViewClient() {
                            override fun shouldOverrideUrlLoading(view: WebView?, url: String?): Boolean {
                                return false // Load all links within the WebView itself
                            }

                            override fun onPageFinished(view: WebView?, url: String?) {
                                // Inject viewport meta tag if missing and attempt to open sidebar
                                val js = """
                                    (function() {
                                        if (!document.querySelector('meta[name=viewport]')) {
                                            var meta = document.createElement('meta');
                                            meta.name = 'viewport';
                                            meta.content = 'width=device-width, initial-scale=1';
                                            document.head.appendChild(meta);
                                        }
                                        // Try to toggle sidebar if button exists
                                        var toggle = document.querySelector('.sidebar-toggle') || document.getElementById('sidebarToggle');
                                        if (toggle) { toggle.click(); }
                                    })();
                                """.trimIndent()
                                view?.evaluateJavascript(js, null)
                            }
                        }

                        // Handle progress changes & file chooser
                        webChromeClient = object : WebChromeClient() {
                            override fun onProgressChanged(view: WebView?, newProgress: Int) {
                                loadingProgress = newProgress
                                isPageLoading = newProgress < 100
                            }

                            override fun onShowFileChooser(
                                webView: WebView?,
                                callback: ValueCallback<Array<Uri>>?,
                                fileChooserParams: FileChooserParams?
                            ): Boolean {
                                filePathCallback?.onReceiveValue(null)
                                filePathCallback = callback

                                val intent = fileChooserParams?.createIntent()
                                if (intent == null) {
                                    filePathCallback?.onReceiveValue(null)
                                    filePathCallback = null
                                    return false
                                }
                                try {
                                    fileChooserLauncher.launch(intent)
                                } catch (e: Exception) {
                                    filePathCallback?.onReceiveValue(null)
                                    filePathCallback = null
                                    return false
                                }
                                return true
                            }
                        }

                        // Load initial URL
                        loadUrl(adminUrl)
                        webViewInstance = this
                    }
                },
                modifier = Modifier.fillMaxSize(),
                update = { webView ->
                    webViewInstance = webView
                }
            )
        }
    }
}

suspend fun uploadImage(uri: Uri, context: Context): Boolean {
    return kotlinx.coroutines.withContext(kotlinx.coroutines.Dispatchers.IO) {
        try {
            val contentResolver = context.contentResolver
            val mimeType = contentResolver.getType(uri) ?: "image/jpeg"
            val extension = android.webkit.MimeTypeMap.getSingleton().getExtensionFromMimeType(mimeType) ?: "jpg"
            val fileName = "upload_logo_${System.currentTimeMillis()}.${extension}"

            val inputStream = contentResolver.openInputStream(uri) ?: return@withContext false
            val bytes = inputStream.readBytes()
            inputStream.close()

            val mediaType = mimeType.toMediaTypeOrNull()
            val reqFile = bytes.toRequestBody(mediaType)

            val requestBody = okhttp3.MultipartBody.Builder()
                .setType(okhttp3.MultipartBody.FORM)
                .addFormDataPart("file", fileName, reqFile)
                .build()

            val request = okhttp3.Request.Builder()
                .url("${com.sportiptv.app.util.Constants.BASE_URL}/api/upload-logo")
                .post(requestBody)
                .build()

            val client = okhttp3.OkHttpClient()
            val response = client.newCall(request).execute()
            response.isSuccessful
        } catch (e: Exception) {
            e.printStackTrace()
            false
        }
    }
}
