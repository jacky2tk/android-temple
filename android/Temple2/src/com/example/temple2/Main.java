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
import java.util.ArrayList;
import java.util.LinkedHashMap;
import java.util.List;
import java.util.Map;

import android.content.Intent;
import android.content.SharedPreferences;
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
import android.widget.ImageButton;
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
	private ImageButton imgSearch, imgSetting;
	
	private SharedPreferences settings;
	String bestProvider = "network";
	private Location CenterPoint = null;
	private String PlaceJSON = "";
	private GeoPoint MapCenter;
	
    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);     
     	requestWindowFeature(Window.FEATURE_NO_TITLE);	// 移除 Title bar
        setContentView(R.layout.main);
        
        imgWiFi = (ImageView)findViewById(R.id.imgWiFi);
        imgGPS = (ImageView)findViewById(R.id.imgGPS);
        edtSearch = (EditText)findViewById(R.id.edtSearch);
        imgSearch = (ImageButton)findViewById(R.id.imgSearch);
        imgSetting = (ImageButton)findViewById(R.id.imgSetting);
        mapView = (MapView)findViewById(R.id.mapview);
        
        settings = getSharedPreferences(Setting.SETTINGS, 0);
        edtSearch.setText("宮");
        
        // 地圖設定
        mapView.setBuiltInZoomControls(true);	// 啟用內建的 Zoom 縮放功能
        mapView.setClickable(true);				// 啟用可 Click 功能
        mapView.getController().setZoom(16);	// 設定地圖縮放比例為 16      
        
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
        	
        	// 若無法取得定位, 改定位到上益電腦
        	// 上益電腦: 24.25164, 120.72034        	
        	if (CenterPoint == null) {
        		CenterPoint.setLatitude(24.25164);
        		CenterPoint.setLongitude(120.72034);
        	}
        	
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
					getResources().getDrawable(R.drawable.cp),
					Main.this);
			
			OverlayItem CenterOverlay = new OverlayItem(MapCenter, "cp", "目前位置");	// 建立中心點地標
			CenterMapOverlay.addOverlay(CenterOverlay);		// 將中心點地標加到圖層
			mapView.getOverlays().add(CenterMapOverlay);	// 將圖層加到 MapView
			
        	 
        } else {
        	Toast.makeText(this, "請開啟定位服務", Toast.LENGTH_LONG).show();
        	startActivity(new Intent(Settings.ACTION_LOCATION_SOURCE_SETTINGS));
        }
        
        // 按鈕：搜尋
        imgSearch.setOnClickListener(new OnClickListener() {
			
			public void onClick(View v) {
				Thread thread = new Thread(new Runnable() {

					public void run() {
						if (CenterPoint != null) {
			        		PlaceJSON = getPlace(
			        				String.valueOf(CenterPoint.getLatitude()) + "," + String.valueOf(CenterPoint.getLongitude()), 
			        				String.valueOf(settings.getInt(Setting.RADIUS, 1500)),
			        				edtSearch.getText().toString());
			        		
			        		SearchHandler.post(SearchSB);
			        	}
					}
	        		
	        	});
	        	
	        	thread.start();
			}
		});
        
        // 按鈕：設定
        imgSetting.setOnClickListener(new OnClickListener() {
			
			public void onClick(View v) {
				Intent intent = new Intent(Main.this, Setting.class);
				startActivity(intent);
			}
		});
    }    
    
    private Handler SearchHandler = new Handler();
	private Runnable SearchSB = new Runnable() {

		public void run() {
			// 神祇圖形陣列
			int[] imgGodAry = {R.drawable.god_1, R.drawable.god_2, R.drawable.god_3,
							   R.drawable.god_4, R.drawable.god_5, R.drawable.god_6,
							   R.drawable.god_7, R.drawable.god_8, R.drawable.god_9,
							   R.drawable.god_10, R.drawable.god_11, R.drawable.god_12,
							   R.drawable.god_13, R.drawable.god_14, R.drawable.god_15,
							   R.drawable.god_16, R.drawable.god_17, R.drawable.god_18,
							   R.drawable.god_19};
			
			mapView.getOverlays().clear();	// 清除所有圖層
			
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
					getResources().getDrawable(R.drawable.cp),
					Main.this);
			
			OverlayItem CenterOverlay = new OverlayItem(MapCenter, "cp", "目前位置");	// 建立中心點地標
			CenterMapOverlay.addOverlay(CenterOverlay);		// 將中心點地標加到圖層
			mapView.getOverlays().add(CenterMapOverlay);	// 將圖層加到 MapView
			
			// --------------------------------------------------------------------------
			// 建立新的圖層, 放找到的地標
			Map<String, Object> gsonData = new LinkedHashMap();
			gsonData = new Gson().fromJson(PlaceJSON, gsonData.getClass());
			
			if (!((String) gsonData.get("status")).equals("OK")) {
				// 找不到任何點
				Toast.makeText(Main.this, "無符合的資料", Toast.LENGTH_LONG).show();
            } else { 	
            	
            	// 建立一個新圖層
    			//ArrayList<MyItemizedOverlay> overlays = new ArrayList<MyItemizedOverlay>();
            	MyItemizedOverlay ItemMapOverlay = new MyItemizedOverlay(
                		getResources().getDrawable(R.drawable.god_1), 
                		Main.this);
    			ItemMapOverlay.setCenter(MapCenter);
    			//overlays.add(ItemMapOverlay);
            	
            	// 將搜尋回傳結果放到 List
			    List result = (List) gsonData.get("results");
			    
			    // 將各點地標加到圖層中
			    for (int i = 0; i < result.size(); i++) {
				    Map<String, Object> res = (Map<String, Object>) result.get(i);
				 
				    // 取出座標
					Map<String, Map<String, Object>> geom =
                        (Map<String, Map<String, Object>>) res.get("geometry");
                
                    Map<String, Object> loc = geom.get("location");
                    Double lat = (Double) loc.get("lat");
                    Double lng = (Double) loc.get("lng");

                    // 將地標加到圖層中
                    GeoPoint PlaceGeo = new GeoPoint(
    					(int)(lat * 1e6), 
    					(int)(lng * 1e6));
    			    OverlayItem PlaceOverlay = new OverlayItem(
    			    		PlaceGeo, (String) res.get("name"), (String) res.get("god"));
    			    int imgIdx = Integer.parseInt(res.get("icon").toString()) - 1;
    			    ItemMapOverlay.addOverlay(
    			    		PlaceOverlay, getResources().getDrawable(imgGodAry[imgIdx]));
    			    
    			    //System.out.println(String.valueOf(i+1) + ". " + res.get("name"));
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
		settings = getSharedPreferences(Setting.SETTINGS, 0);
		try {
			
			// http://localhost/temple/agent.php?case=temple_list&search=%E5%BB%9F&locat=24.2557,120.7205&dist=0.4
			// 上益電腦: 24.2557, 120.7205
			StringBuilder urlSB = new StringBuilder();			
			//urlSB.append("http://" + getString(R.string.server_ip) + "/Temple/Agent.php");
			urlSB.append("http://temple.html-5.me/agent.php");
			urlSB.append("?case=temple_list");
			urlSB.append("&search=" + encodeURIComponent(search));
			urlSB.append("&locat=" + point);
			urlSB.append("&dist=" + radius);
			System.out.println("urlSB = " + urlSB.toString());
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
