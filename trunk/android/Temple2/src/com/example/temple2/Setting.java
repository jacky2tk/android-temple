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
import android.view.Window;
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
        
        // 增加選單項目
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
						(LinkedHashMap<String, Object>)listView.getItemAtPosition(0);
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
						(LinkedHashMap<String, Object>)listView.getItemAtPosition(0);
				View SeekView = (View)ItemInfo.get("VIEW");
				SeekBar seekBar = (SeekBar)SeekView.findViewById(R.id.seekBar_radius);
				seekBar.setProgress(Integer.parseInt(s.toString()));
				
				settings.edit()
					.putInt(RADIUS, seekBar.getProgress())
					.commit();
			}
			
		});
        
        
        // 將 adapter 指定到 ListView
        listView.setAdapter(adapter);
        
	}
}
