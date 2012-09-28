package com.example.googlemap;

import android.app.Activity;
import android.content.Context;
import android.graphics.Bitmap;
import android.webkit.WebView;
import android.webkit.WebViewClient;
import android.widget.Toast;

public class MyWebViewClient extends WebViewClient{
	Context context;
	public MyWebViewClient(Context c){
		this.context = c;
	}
	@Override
	public void onPageFinished(WebView view, String url) {
		// TODO Auto-generated method stub
		((Activity)context).setTitle(R.string.app_name);
		super.onPageFinished(view, url);
	}
	@Override
	public void onPageStarted(WebView view, String url, Bitmap favicon) {
		// TODO Auto-generated method stub
		((Activity)context).setTitle("正在連線中...");
		super.onPageStarted(view, url, favicon);
	}
	@Override
	public void onReceivedError(WebView view, int errorCode,
			String description, String failingUrl) {
		// TODO Auto-generated method stub
		Toast.makeText(context, description, Toast.LENGTH_LONG).show();
		super.onReceivedError(view, errorCode, description, failingUrl);
	}
	
}
