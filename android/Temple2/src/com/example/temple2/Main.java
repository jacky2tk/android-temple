package com.example.temple2;

import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStreamReader;
import java.io.StringWriter;
import java.io.UnsupportedEncodingException;
import java.net.HttpURLConnection;
import java.net.MalformedURLException;
import java.net.URL;
import java.net.URLEncoder;
import java.util.LinkedHashMap;
import java.util.List;
import java.util.Map;

import android.content.Intent;
import android.location.Criteria;
import android.location.Location;
import android.location.LocationListener;
import android.location.LocationManager;
import android.location.LocationProvider;
import android.os.Bundle;
import android.os.Handler;
import android.provider.Settings;
import android.view.Menu;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.Window;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.Toast;

import com.google.android.maps.GeoPoint;
import com.google.android.maps.MapActivity;
import com.google.android.maps.MapView;
import com.google.android.maps.OverlayItem;
import com.google.gson.Gson;

public class Main extends MapActivity implements LocationListener {
	
	private MapView mapView;
	private LocationManager mLocationManager;
	private ImageView imgWiFi, imgGPS;
	private EditText edtSearch;
	private Button btnSearch, btnSetting;
	
	String bestProvider = "network";
	private Location CenterPoint = null;
	private String PlaceJSON = "";

    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);     
     	requestWindowFeature(Window.FEATURE_NO_TITLE);	// 移除 Title bar
        setContentView(R.layout.main);
        
        imgWiFi = (ImageView)findViewById(R.id.imgWiFi);
        imgGPS = (ImageView)findViewById(R.id.imgGPS);
        edtSearch = (EditText)findViewById(R.id.edtSearch);
        btnSearch = (Button)findViewById(R.id.btnSearch);
        btnSetting = (Button)findViewById(R.id.btnSetting);
        mapView = (MapView)findViewById(R.id.mapview);
        mapView.setBuiltInZoomControls(true);	// 啟用內建的 Zoom 縮放功能
        mapView.setClickable(true);				// 啟用可 Click 功能
        mapView.getController().setZoom(16);	// 設定地圖縮放比例為 16
        
        edtSearch.setText("廟");
        
        mLocationManager = (LocationManager)getSystemService(LOCATION_SERVICE);
        if (mLocationManager.isProviderEnabled(LocationManager.GPS_PROVIDER) ||
        	mLocationManager.isProviderEnabled(LocationManager.NETWORK_PROVIDER)){
        	
        	// 設定 WiFi 圖示
        	if (mLocationManager.isProviderEnabled(LocationManager.NETWORK_PROVIDER)) {
        		imgWiFi.setImageResource(R.drawable.wifi_on);
        	} else {
        		imgWiFi.setImageResource(R.drawable.wifi_off);
        	}
        	
        	// 設定 GPS 圖示
        	if (mLocationManager.isProviderEnabled(LocationManager.GPS_PROVIDER)) {
        		imgGPS.setImageResource(R.drawable.gps_on);
        	} else {
        		imgGPS.setImageResource(R.drawable.gps_off);
        	}

        	// 透過 Criteria 取得目前最好的定位 Provider
        	Criteria criteria = new Criteria();
        	bestProvider = mLocationManager.getBestProvider(criteria, true);
        	mLocationManager.requestLocationUpdates(bestProvider, 0, 0, this);
        	
        	CenterPoint = mLocationManager.getLastKnownLocation(bestProvider);    	
        	
        	// --------------------------------------------------------------------------
			// 建立圖層, 產生中心地圖
			
			// 指定地圖中心點座標
			GeoPoint MapCenter = new GeoPoint(
					(int)(CenterPoint.getLatitude() * 1e6),
					(int)(CenterPoint.getLongitude() * 1e6));
			
			// 設定地圖中心點
			mapView.getController().setCenter(MapCenter);
			
			// 建立中心點圖層, 設定中心點圖示
			MyItemizedOverlay CenterMapOverlay = new MyItemizedOverlay(
					getResources().getDrawable(R.drawable.batman),
					Main.this);
			
			OverlayItem CenterOverlay = new OverlayItem(MapCenter, "", "");	// 建立中心點地標
			CenterMapOverlay.addOverlay(CenterOverlay);		// 將中心點地標加到圖層
			mapView.getOverlays().add(CenterMapOverlay);	// 將圖層加到 MapView
        	
        	 
        } else {
        	Toast.makeText(this, "請開啟定位服務", Toast.LENGTH_LONG).show();
        	startActivity(new Intent(Settings.ACTION_LOCATION_SOURCE_SETTINGS));
        }
        
        btnSearch.setOnClickListener(new OnClickListener() {
			
			public void onClick(View v) {
				Thread thread = new Thread(new Runnable() {

					public void run() {
						if (CenterPoint != null) {
			        		//SharedPreferences settings = getSharedPreferences(Setting.SETTINGS, 0);
			        		PlaceJSON = getPlace(
			        				String.valueOf(CenterPoint.getLatitude()) + "," + String.valueOf(CenterPoint.getLongitude()), 
			        				//String.valueOf(settings.getInt(Setting.RADIUS, 1500)),
			        				String.valueOf(200),
			        				//getIntent().getExtras().getString("Search")
			        				edtSearch.getText().toString());
			        		
			        		SearchHandler.post(SearchSB);
			        	}
					}
	        		
	        	});
	        	
	        	thread.start();
			}
		});
        
        btnSetting.setOnClickListener(new OnClickListener() {
			
			public void onClick(View v) {
//				Intent intent = new Intent(Main.this, Setting.class);
//				startActivity(intent);
			}
		});
    }    
    
    private Handler SearchHandler = new Handler();
	private Runnable SearchSB = new Runnable() {

		public void run() {			
			// --------------------------------------------------------------------------
			// 建立新的圖層, 放找到的地標
			Map<String, Object> gsonData = new LinkedHashMap();
			gsonData = new Gson().fromJson(PlaceJSON, gsonData.getClass());
			
			if (!((String) gsonData.get("status")).equals("OK")) {
				// 找不到任何點
				Toast.makeText(Main.this, "Zero Results", Toast.LENGTH_LONG).show();
            } else {
            	// 建立一個新圖層
            	MyItemizedOverlay ItemMapOverlay = new MyItemizedOverlay(
                		getResources().getDrawable(R.drawable.batman_logo), 
                		Main.this);
            	
            	// 將搜尋回傳結果放到 List
			    List result = (List) gsonData.get("results");
			    
			    // 將各點地標加到圖層中
			    for (int i = 0; i < result.size(); i++) {
				    Map<String, Object> res = (Map<String, Object>) result.get(i);
				 
					Map<String, Map<String, Object>> geom =
                        (Map<String, Map<String, Object>>) res.get("geometry");
                
                    Map<String, Object> loc = geom.get("location");
                    Double lat = (Double) loc.get("lat");
                    Double lng = (Double) loc.get("lng");

                    GeoPoint PlaceGeo = new GeoPoint(
    					(int)(lat * 1e6), 
    					(int)(lng * 1e6));
    			    OverlayItem PlaceOverlay = new OverlayItem(PlaceGeo, (String) res.get("name"), (String) res.get("address"));
    			    ItemMapOverlay.addOverlay(PlaceOverlay);
			    }
			    
			    // 將圖層加到 MapView
			    mapView.getOverlays().add(ItemMapOverlay);
            }
		}
		
	};
	
	// 搜尋座標附近的地標, 回傳 JSON 資料格式
	private String getPlace(String point, String radius, String search) {
		StringWriter SW = new StringWriter();
		HttpURLConnection conn = null;
		try {
			
			// http://localhost/temple/agent.php?case=temple_list&search=%E5%BB%9F&locat=24.2557,120.7205&dist=0.4
			// 上益電腦: 24.2557, 120.7205
			StringBuilder urlSB = new StringBuilder();			
			urlSB.append("http://" + getString(R.string.server_ip) + "/Temple/Agent.php");
			urlSB.append("?case=temple_list");
			urlSB.append("&search=" + edtSearch.getText().toString());
			urlSB.append("&location=" + "24.2557,120.7205");
//			if (location != null) { 
//				urlSB.append(String.valueOf(location.getLatitude()) + "," + String.valueOf(location.getLongitude()));
//			}
			urlSB.append("&dist=200");
			System.out.println("urlSB = " + urlSB.toString());
			//Toast.makeText(Main.this, urlSB.toString(), Toast.LENGTH_LONG).show();
			URL fileURL = new URL(urlSB.toString());
			
			
			/* Google Map
			StringBuilder PathSB = new StringBuilder();
			PathSB.append("https://maps.googleapis.com/maps/api/place/search/json");
			PathSB.append("?location=" + point);
			PathSB.append("&radius=" + radius);
			PathSB.append("&name=" + encodeURIComponent(search));
			PathSB.append("&sensor=true&key=AIzaSyB0bG95zf_mVZSeejAkQwCAS8NemHcG9Do");
			PathSB.append("&language=" + "zh-TW");
			System.out.println(PathSB.toString());
			String GETStr = PathSB.toString();
			
			URL fileURL = new URL(GETStr);
			*/
			
			//使用openConnection方法取得HttpUrlConnection
			conn = (HttpURLConnection) fileURL.openConnection();
            //連接
  			conn.connect(); 
  			
			char[] buffer = new char[1024];
			BufferedReader reader = null;
			try {
				reader = new BufferedReader(
					new InputStreamReader(conn.getInputStream(), "UTF-8"));
			    int n;
			    while ((n = reader.read(buffer)) != -1) {
				    SW.write(buffer, 0, n);
			    }
			    
			} catch (UnsupportedEncodingException e) {
				System.out.println(e.getMessage());
			} catch (IOException e) {
				System.out.println(e.getMessage());
			} finally {
				try {
					if (reader != null){
					    reader.close();
					}
				} catch (IOException e) {
					System.out.println(e.getMessage());
				}
			}
		} catch (MalformedURLException e) {
			System.out.println(e.getMessage());
		} catch (Exception e) {
			System.out.println(e.getMessage());
		} finally {
			if (conn != null){
			    conn.disconnect();
			}
		}
		
		System.out.println("Receive = " + SW.toString());
		
		return SW.toString();
	}
	
	private String encodeURIComponent(String component)   {     
    	String result = null;      
    	
    	try {       
    		result = URLEncoder.encode(component, "UTF-8")   
    			   .replaceAll("\\%28", "(")                          
    			   .replaceAll("\\%29", ")")   		
    			   .replaceAll("\\+", "%20")                          
    			   .replaceAll("\\%27", "'")   			   
    			   .replaceAll("\\%21", "!")
    			   .replaceAll("\\%7E", "~");  
    	}
    	catch (UnsupportedEncodingException e) {       
    		result = component;     
    	}      
    	
    	return result;   
    }

    @Override
    public boolean onCreateOptionsMenu(Menu menu) {
        getMenuInflater().inflate(R.menu.main, menu);
        return true;
    }

	public void onLocationChanged(Location location) {
		// TODO Auto-generated method stub
		
	}

	public void onProviderDisabled(String provider) {
		// TODO Auto-generated method stub
		
	}

	public void onProviderEnabled(String provider) {
		// TODO Auto-generated method stub
		
	}

	public void onStatusChanged(String provider, int status, Bundle extras) {
		switch (status) {
		case LocationProvider.AVAILABLE:
			Toast.makeText(Main.this, "AVAILABLE", Toast.LENGTH_SHORT).show();
			break;
			
		case LocationProvider.OUT_OF_SERVICE:
			Toast.makeText(Main.this, "OUT_OF_SERVICE", Toast.LENGTH_SHORT).show();
			break;
			
		case LocationProvider.TEMPORARILY_UNAVAILABLE:
			Toast.makeText(Main.this, "TEMPORARILY_UNAVAILABLE", Toast.LENGTH_SHORT).show();
			break;
		}
	}

	@Override
	protected boolean isRouteDisplayed() {
		// TODO Auto-generated method stub
		return false;
	}
}
