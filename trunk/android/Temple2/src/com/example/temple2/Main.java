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
     	requestWindowFeature(Window.FEATURE_NO_TITLE);	// ���� Title bar
        setContentView(R.layout.main);
        
        imgWiFi = (ImageView)findViewById(R.id.imgWiFi);
        imgGPS = (ImageView)findViewById(R.id.imgGPS);
        edtSearch = (EditText)findViewById(R.id.edtSearch);
        imgSearch = (ImageButton)findViewById(R.id.imgSearch);
        imgSetting = (ImageButton)findViewById(R.id.imgSetting);
        mapView = (MapView)findViewById(R.id.mapview);
        
        settings = getSharedPreferences(Setting.SETTINGS, 0);
        edtSearch.setText("�c");
        
        // �a�ϳ]�w
        mapView.setBuiltInZoomControls(true);	// �ҥΤ��ت� Zoom �Y��\��
        mapView.setClickable(true);				// �ҥΥi Click �\��
        mapView.getController().setZoom(16);	// �]�w�a���Y���Ҭ� 16      
        
        mLocationManager = (LocationManager)getSystemService(LOCATION_SERVICE);
        if (mLocationManager.isProviderEnabled(LocationManager.GPS_PROVIDER) ||
        	mLocationManager.isProviderEnabled(LocationManager.NETWORK_PROVIDER)){
        	
        	// �]�w WiFi �ϥ�
        	if (mLocationManager.isProviderEnabled(LocationManager.NETWORK_PROVIDER)) {
        		imgWiFi.setImageResource(R.drawable.wifi_on);
        	} else {
        		imgWiFi.setImageResource(R.drawable.wifi_off);
        	}
        	
        	// �]�w GPS �ϥ�
        	if (mLocationManager.isProviderEnabled(LocationManager.GPS_PROVIDER)) {
        		imgGPS.setImageResource(R.drawable.gps_on);
        	} else {
        		imgGPS.setImageResource(R.drawable.gps_off);
        	}

        	// �z�L Criteria ���o�ثe�̦n���w�� Provider
        	Criteria criteria = new Criteria();
        	bestProvider = mLocationManager.getBestProvider(criteria, true);
        	mLocationManager.requestLocationUpdates(bestProvider, 0, 0, this);
        	
        	CenterPoint = mLocationManager.getLastKnownLocation(bestProvider);
        	
        	// �Y�L�k���o�w��, ��w���W�q�q��
        	// �W�q�q��: 24.25164, 120.72034        	
        	if (CenterPoint == null) {
        		CenterPoint.setLatitude(24.25164);
        		CenterPoint.setLongitude(120.72034);
        	}
        	
        	// --------------------------------------------------------------------------
			// �إ߹ϼh, ���ͤ��ߦa��
			
			// ���w�a�Ϥ����I�y��
			GeoPoint MapCenter = new GeoPoint(
					(int)(CenterPoint.getLatitude() * 1e6),
					(int)(CenterPoint.getLongitude() * 1e6));
			
			// �]�w�a�Ϥ����I
			mapView.getController().setCenter(MapCenter);
			
			// �إߤ����I�ϼh, �]�w�����I�ϥ�
			MyItemizedOverlay CenterMapOverlay = new MyItemizedOverlay(
					getResources().getDrawable(R.drawable.cp),
					Main.this);
			
			OverlayItem CenterOverlay = new OverlayItem(MapCenter, "cp", "�ثe��m");	// �إߤ����I�a��
			CenterMapOverlay.addOverlay(CenterOverlay);		// �N�����I�a�Х[��ϼh
			mapView.getOverlays().add(CenterMapOverlay);	// �N�ϼh�[�� MapView
			
        	 
        } else {
        	Toast.makeText(this, "�ж}�ҩw��A��", Toast.LENGTH_LONG).show();
        	startActivity(new Intent(Settings.ACTION_LOCATION_SOURCE_SETTINGS));
        }
        
        // ���s�G�j�M
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
        
        // ���s�G�]�w
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
			// ����ϧΰ}�C
			int[] imgGodAry = {R.drawable.god_1, R.drawable.god_2, R.drawable.god_3,
							   R.drawable.god_4, R.drawable.god_5, R.drawable.god_6,
							   R.drawable.god_7, R.drawable.god_8, R.drawable.god_9,
							   R.drawable.god_10, R.drawable.god_11, R.drawable.god_12,
							   R.drawable.god_13, R.drawable.god_14, R.drawable.god_15,
							   R.drawable.god_16, R.drawable.god_17, R.drawable.god_18,
							   R.drawable.god_19};
			
			mapView.getOverlays().clear();	// �M���Ҧ��ϼh
			
			// --------------------------------------------------------------------------
			// �إ߹ϼh, ���ͤ��ߦa��
			
			// ���w�a�Ϥ����I�y��
			GeoPoint MapCenter = new GeoPoint(
					(int)(CenterPoint.getLatitude() * 1e6),
					(int)(CenterPoint.getLongitude() * 1e6));
			
			// �]�w�a�Ϥ����I
			mapView.getController().setCenter(MapCenter);
			
			// �إߤ����I�ϼh, �]�w�����I�ϥ�
			MyItemizedOverlay CenterMapOverlay = new MyItemizedOverlay(
					getResources().getDrawable(R.drawable.cp),
					Main.this);
			
			OverlayItem CenterOverlay = new OverlayItem(MapCenter, "cp", "�ثe��m");	// �إߤ����I�a��
			CenterMapOverlay.addOverlay(CenterOverlay);		// �N�����I�a�Х[��ϼh
			mapView.getOverlays().add(CenterMapOverlay);	// �N�ϼh�[�� MapView
			
			// --------------------------------------------------------------------------
			// �إ߷s���ϼh, ���쪺�a��
			Map<String, Object> gsonData = new LinkedHashMap();
			gsonData = new Gson().fromJson(PlaceJSON, gsonData.getClass());
			
			if (!((String) gsonData.get("status")).equals("OK")) {
				// �䤣������I
				Toast.makeText(Main.this, "�L�ŦX�����", Toast.LENGTH_LONG).show();
            } else { 	
            	
            	// �إߤ@�ӷs�ϼh
    			//ArrayList<MyItemizedOverlay> overlays = new ArrayList<MyItemizedOverlay>();
            	MyItemizedOverlay ItemMapOverlay = new MyItemizedOverlay(
                		getResources().getDrawable(R.drawable.god_1), 
                		Main.this);
    			ItemMapOverlay.setCenter(MapCenter);
    			//overlays.add(ItemMapOverlay);
            	
            	// �N�j�M�^�ǵ��G��� List
			    List result = (List) gsonData.get("results");
			    
			    // �N�U�I�a�Х[��ϼh��
			    for (int i = 0; i < result.size(); i++) {
				    Map<String, Object> res = (Map<String, Object>) result.get(i);
				 
				    // ���X�y��
					Map<String, Map<String, Object>> geom =
                        (Map<String, Map<String, Object>>) res.get("geometry");
                
                    Map<String, Object> loc = geom.get("location");
                    Double lat = (Double) loc.get("lat");
                    Double lng = (Double) loc.get("lng");

                    // �N�a�Х[��ϼh��
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
			    
			    // �N�ϼh�[�� MapView
			    mapView.getOverlays().add(ItemMapOverlay);
            }
		}
		
	};
	
	// �j�M�y�Ъ��񪺦a��, �^�� JSON ��Ʈ榡
	private String getPlace(String point, String radius, String search) {
		StringWriter SW = new StringWriter();
		HttpURLConnection conn = null;
		settings = getSharedPreferences(Setting.SETTINGS, 0);
		try {
			
			// http://localhost/temple/agent.php?case=temple_list&search=%E5%BB%9F&locat=24.2557,120.7205&dist=0.4
			// �W�q�q��: 24.2557, 120.7205
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
			
			//�ϥ�openConnection��k���oHttpUrlConnection
			conn = (HttpURLConnection) fileURL.openConnection();
            //�s��
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
