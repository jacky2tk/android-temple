package com.example.googlemap;

import android.app.Activity;
import android.location.LocationManager;
import android.os.Bundle;
import android.webkit.WebView;
import android.widget.Button;
import android.widget.EditText;
import android.widget.RadioButton;

public class Main extends Activity {

	private LocationManager mLocationManager;
	private EditText editText1;
	private RadioButton radiobutton1;
	private Button button1;
	private WebView mWebView;
	
    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.main);
        
        radiobutton1 = (RadioButton)findViewById(R.id.radioButton1);
        editText1 = (EditText)findViewById(R.id.editText1);
        button1 = (Button)findViewById(R.id.button1);
        mWebView = (WebView)findViewById(R.id.webView1);
        
        mWebView.loadUrl("http://192.168.0.227:8080/AndroidWeb/googlemap.html");
        
    }

}
