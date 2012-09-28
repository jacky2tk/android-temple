package com.example.googlemap;

import android.app.Activity;
import android.location.LocationManager;
import android.os.Bundle;
import android.webkit.WebView;
import android.webkit.WebViewClient;
import android.widget.Button;
import android.widget.EditText;
import android.widget.RadioButton;

public class Main extends Activity {

	private LocationManager mLocationManager;
	private EditText mEditText;
	private RadioButton mRadiobutton;
	private Button mButton;
	private WebView mWebView;
	
    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.main);
        
        mRadiobutton = (RadioButton)findViewById(R.id.radioButton1);
        mEditText = (EditText)findViewById(R.id.editText1);
        mButton = (Button)findViewById(R.id.button1);
        mWebView = (WebView)findViewById(R.id.webView1);
        mWebView.getSettings().setJavaScriptEnabled(true);
        
        mWebView.loadUrl("http://192.168.31.1:8080/TempleWeb/googlemap.html");
        
        mWebView.setWebViewClient(new MyWebViewClient());
        
    }
    private class MyWebViewClient extends WebViewClient{
    	public boolean shouldOverrideUrlLoadin(WebView view , String url){
    		view.loadUrl(url);
    		return true;
    	}
    }
}
