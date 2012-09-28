package com.example.googlemap;

import android.app.AlertDialog;
import android.content.Context;
import android.content.DialogInterface;
import android.content.DialogInterface.OnClickListener;
import android.net.rtp.RtpStream;
import android.webkit.GeolocationPermissions.Callback;
import android.webkit.JsPromptResult;
import android.webkit.JsResult;
import android.webkit.WebChromeClient;
import android.webkit.WebView;
import android.webkit.WebViewClient;

public class MyWebChromeClinet extends WebChromeClient {
	Context context;
	public MyWebChromeClinet(Context c){
		this.context = c;
	}
	@Override
	public void onGeolocationPermissionsShowPrompt(final String origin,
			final Callback callback) {
		//final boolean remeber = false;
		AlertDialog.Builder builder = new AlertDialog.Builder(context);
		builder.setTitle("透露位置資訊");
		builder
		.setMessage("您願意把您的位置透露給我們知道嗎?")
		.setCancelable(true)
		.setPositiveButton("願意", new DialogInterface.OnClickListener() {
			
			public void onClick(DialogInterface dialog, int which) {
				callback.invoke(origin, true, true);
			}
		})
		.setNegativeButton("不願意", new DialogInterface.OnClickListener() {
			
			public void onClick(DialogInterface dialog, int which) {
				callback.invoke(origin, false, false);
			}
		});
		AlertDialog alert = builder.create();
		alert.show();
	}
	@Override
	public boolean onJsAlert(WebView view, String url, String message,
			final JsResult result) {
		AlertDialog.Builder builder = new AlertDialog.Builder(context);
		builder.setTitle("Alert");
		builder.setMessage(message);
		builder.setPositiveButton(android.R.string.ok , new OnClickListener() {
			
			public void onClick(DialogInterface dialog, int which) {
				// TODO Auto-generated method stub
				result.confirm();
			}
		});
		builder.setCancelable(false);
		builder.create();
		builder.show();
		return true;
	}
	@Override
	public boolean onJsConfirm(WebView view, String url, String message,
			JsResult result) {
		// TODO Auto-generated method stub
		return super.onJsConfirm(view, url, message, result);
	}
	@Override
	public boolean onJsPrompt(WebView view, String url, String message,
			String defaultValue, JsPromptResult result) {
		// TODO Auto-generated method stub
		return super.onJsPrompt(view, url, message, defaultValue, result);
	}
	@Override
	public void onReceivedTitle(WebView view, String title) {
		// TODO Auto-generated method stub
		super.onReceivedTitle(view, title);
	}
	
	
}
