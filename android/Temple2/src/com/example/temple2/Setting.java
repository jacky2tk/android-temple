package com.example.temple2;

import java.util.LinkedHashMap;

import android.app.AlertDialog;
import android.app.ListActivity;
import android.content.DialogInterface;
import android.content.SharedPreferences;
import android.graphics.drawable.ColorDrawable;
import android.os.Bundle;
import android.text.Editable;
import android.text.TextWatcher;
import android.view.LayoutInflater;
import android.view.View;
import android.widget.AdapterView;
import android.widget.AdapterView.OnItemClickListener;
import android.widget.EditText;
import android.widget.ListView;
import android.widget.SeekBar;
import android.widget.TextView;

public class Setting extends ListActivity {
	private ListView listView;
	
	private SharedPreferences settings;
	public static String SETTINGS = "SETTINGS";
	public static String RADIUS = "RADIUS";
	
	@Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        
        settings = getSharedPreferences(SETTINGS, 0);
        
        listView = getListView();
        listView.setBackgroundColor(getResources().getColor(android.R.color.black));
        listView.setDivider(new ColorDrawable(getResources().getColor(android.R.color.darker_gray)));
        listView.setDividerHeight(1);
        
        CustomAdapter adapter = new CustomAdapter(
        		(LayoutInflater)getSystemService(LAYOUT_INFLATER_SERVICE),
        		this, getResources());
        
        // �W�[��涵��
//        adapter.addListItem2("�d�߻y��", 
//        		settings.getString(LANG, "zh-TW").equals("zh-TW")? "�c�餤��(zh-TW)": "�^��(en-US)");
//        
//        adapter.addListItem2("�ɯ豱��",
//        		settings.getString(NAVTYPE, "WebView").equals("WebView")? "���� (WebView)": "���� (MapView)");
        
        adapter.addSeekBar(settings.getInt(RADIUS, 1500), 10000, new SeekBar.OnSeekBarChangeListener() {
			
			public void onStopTrackingTouch(SeekBar seekBar) {
				// TODO Auto-generated method stub
				
			}
			
			public void onStartTrackingTouch(SeekBar seekBar) {
				// TODO Auto-generated method stub
				
			}
			
			public void onProgressChanged(SeekBar seekBar, int progress,
					boolean fromUser) {
				LinkedHashMap<String, Object> ItemInfo = 
						(LinkedHashMap<String, Object>)listView.getItemAtPosition(2);
				View SeekView = (View)ItemInfo.get("VIEW");
				EditText txtView = (EditText)SeekView.findViewById(R.id.Txt_Radius);
				txtView.setText(String.valueOf(progress));
				
				settings.edit()
					.putInt(RADIUS, progress)
					.commit();
			}
		}, 
		new TextWatcher() {

			public void afterTextChanged(Editable s) {
				// TODO Auto-generated method stub
				
			}

			public void beforeTextChanged(CharSequence s, int start, int count,
					int after) {
				// TODO Auto-generated method stub
				
			}

			public void onTextChanged(CharSequence s, int start, int before,
					int count) {
				LinkedHashMap<String, Object> ItemInfo = 
						(LinkedHashMap<String, Object>)listView.getItemAtPosition(2);
				View SeekView = (View)ItemInfo.get("VIEW");
				SeekBar seekBar = (SeekBar)SeekView.findViewById(R.id.seekBar_radius);
				seekBar.setProgress(Integer.parseInt(s.toString()));
				
				settings.edit()
					.putInt(RADIUS, seekBar.getProgress())
					.commit();
			}
			
		});
        
        
        // �N adapter ���w�� ListView
        listView.setAdapter(adapter);
        /*
        // ��涵�ت���ť��
        listView.setOnItemClickListener(new OnItemClickListener() {

			public void onItemClick(AdapterView<?> arg0, final View view, int position,
					long id) {
				
				AlertDialog.Builder dialog = new AlertDialog.Builder(Setting.this);
//				switch (position) {
//				case 0:	// �d�߻y��
//					final String langAry[] = new String[]{"�c�餤��", "�^��"};
//					final String langCodeAry[] = new String[]{"zh-TW", "en-US"};
//					
//					dialog.setItems(langAry, new DialogInterface.OnClickListener() {
//
//						public void onClick(DialogInterface dialog, int index) {
//							TextView Lab_Content = (TextView)view.findViewById(android.R.id.text2);
//							
//							Lab_Content.setText(langAry[index] + "(" + langCodeAry[index] + ")");
//							
//							settings.edit()
//								.putString(LANG, langCodeAry[index])
//								.commit();
//						}
//						
//					});
//					dialog.setTitle("�d�߻y��");
//					dialog.show();
//					break;
//					
//				case 1:	// �ɯ豱��
//					final String NaviAry[] = new String[]{"����", "����"};
//					final String NaviEngAry[] = new String[]{"WebView", "MapView"};
//					
//					dialog.setItems(NaviAry, new DialogInterface.OnClickListener() {
//						
//						public void onClick(DialogInterface dialog, int index) {
//							TextView Lab_Content = (TextView)view.findViewById(android.R.id.text2);
//							
//							Lab_Content.setText(NaviAry[index] + "(" + NaviEngAry[index] + ")");
//							String navType = NaviEngAry[index];
//							
//							settings.edit()
//								.putString(NAVTYPE, NaviEngAry[index])
//								.commit();
//						}
//					});
//					dialog.setTitle("�ɯ豱��");
//					dialog.show();
//					break;
//				}
			}
		});*/
	}
}
