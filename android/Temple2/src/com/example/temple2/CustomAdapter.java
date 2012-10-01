package com.example.temple2;

import java.util.ArrayList;
import java.util.LinkedHashMap;

import android.content.Context;
import android.content.SharedPreferences;
import android.content.res.Resources;
import android.text.TextWatcher;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.BaseAdapter;
import android.widget.CheckedTextView;
import android.widget.EditText;
import android.widget.SeekBar;
import android.widget.Spinner;
import android.widget.TextView;
import android.widget.SeekBar.OnSeekBarChangeListener;

public class CustomAdapter extends BaseAdapter {
	/*Variables*/
	private long current = System.currentTimeMillis();
	//
	private ArrayList<LinkedHashMap<String,Object>> TypeList = 
			new ArrayList<LinkedHashMap<String,Object>>();
	
	/*Item Type*/
	public static final int SEEK_ITEM = 2;
	public static final int LIST_ITEM_2 = 4;

	/*Constructor*/
	public CustomAdapter() {
    }
	
	//Instantiates a layout XML file into its corresponding View objects.
	LayoutInflater mInflater;
	Context context;
	Resources res;
	public CustomAdapter(LayoutInflater mInflater, Context c, Resources r) {
        this.mInflater = mInflater;
        this.context = c;
        this.res = r;
    }
	
	/*properties*/
	public ArrayList<LinkedHashMap<String,Object>> getData(){
		return TypeList;
	}
	
	public void setInflater(LayoutInflater mInflater) {
        this.mInflater = mInflater;
    }
	
	/*methods*/
	public View addListItem2(String TitleText, String ContentText){
		View ItemView = mInflater.inflate(android.R.layout.simple_list_item_2, null);
		
		LinkedHashMap<String, Object> ItemList = new LinkedHashMap<String, Object>();
		ItemList.put("ID", current++);
		ItemList.put("ItemType", LIST_ITEM_2);
		ItemList.put("VIEW", ItemView);
		ItemList.put("TitleText", TitleText);
		ItemList.put("ContentText", ContentText);
		TypeList.add(ItemList);
		
		return ItemView;
	}

	//public View addSeekBar(int radius, OnSeekBarChangeListener SBCL, TextWatcher textwatcher){
	public View addSeekBar(int radius, int maxRadius, OnSeekBarChangeListener SBCL, TextWatcher textwatcher){
		View SeekView = mInflater.inflate(R.layout.radius, null);
		
		SeekBar SeekBar_Radius = (SeekBar)SeekView.findViewById(R.id.seekBar_radius);
		SeekBar_Radius.setOnSeekBarChangeListener(SBCL);
		
		EditText txtView = (EditText)SeekView.findViewById(R.id.Txt_Radius);
		txtView.addTextChangedListener(textwatcher);
		
		LinkedHashMap<String, Object> ItemList = new LinkedHashMap<String, Object>();
		ItemList.put("ID", current++);
		ItemList.put("ItemType", SEEK_ITEM);
		ItemList.put("VIEW", SeekView);
		ItemList.put("Radius", radius);
		ItemList.put("MaxRadius", maxRadius);
		TypeList.add(ItemList);
		
		return SeekView;
	}
	
	public void remove(int position) {
		TypeList.remove(position);
	}
	
	/*BaseAdapter Implement Methods*/
	//@Override
	public int getCount() {
		return TypeList.size();
	}

	//@Override
	public Object getItem(int position) {
		return TypeList.get(position);
	}

	//@Override
	public long getItemId(int position) {
		return Long.valueOf(String.valueOf(TypeList.get(position).get("ID")));
	}

	//@Override
	public View getView(int position, View convertView, ViewGroup parent) {
		LinkedHashMap<String,Object> ItemInfo = TypeList.get(position);
        int type = (Integer)ItemInfo.get("ItemType");
        convertView = (View)ItemInfo.get("VIEW");
 
        switch (type){
 	      case LIST_ITEM_2:
   	         TextView Lab_Header = (TextView)convertView.findViewById(android.R.id.text1);
   	         TextView Lab_Content = (TextView)convertView.findViewById(android.R.id.text2);
   	         
   	         if (Lab_Header.getText().toString().equals("") || 
	   	    	 Lab_Header.getText().toString().equals((String)ItemInfo.get("TitleText"))) {   	    	  
	   	    	 Lab_Header.setText((String)ItemInfo.get("TitleText"));
	   	    	 Lab_Header.setTextColor(res.getColor(android.R.color.white));
   	         }

   	         if (Lab_Content.getText().toString().equals("") || 
   	    		 Lab_Content.getText().toString().equals((String)ItemInfo.get("ContentText"))) {
   	    		 Lab_Content.setText((String)ItemInfo.get("ContentText"));
   	    		 Lab_Content.setTextColor(res.getColor(android.R.color.white));
   	    	 }
             break;
             
          case SEEK_ITEM:
        	 EditText TxtView = (EditText)convertView.findViewById(R.id.Txt_Radius);
    	     SeekBar SeekView = (SeekBar)convertView.findViewById(R.id.seekBar_radius);
    	     
    	     String radius = String.valueOf(ItemInfo.get("Radius"));
    	     if (SeekView.getProgress() == 0 || 
    	         SeekView.getProgress() == Integer.parseInt(radius)) {
	    	     TxtView.setText(radius);
	    	     SeekView.setMax((Integer)ItemInfo.get("MaxRadius"));
	    	     SeekView.setProgress(Integer.parseInt(radius));
    	     }
             break;
        }
       
        return convertView;
	}

}
