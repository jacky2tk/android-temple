package com.example.temple2;

import java.util.ArrayList;
import java.util.Iterator;

import android.app.AlertDialog;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.graphics.Canvas;
import android.graphics.Color;
import android.graphics.Paint;
import android.graphics.Point;
import android.graphics.drawable.Drawable;
import android.net.Uri;

import com.google.android.maps.GeoPoint;
import com.google.android.maps.ItemizedOverlay;
import com.google.android.maps.MapView;
import com.google.android.maps.OverlayItem;
import com.google.android.maps.Projection;

public class MyItemizedOverlay extends ItemizedOverlay {
	private ArrayList<OverlayItem> Overlays = new ArrayList<OverlayItem>();
	private Context mContext;

	public MyItemizedOverlay(Drawable defaultMarker, Context c) {
		super(boundCenterBottom(defaultMarker));
		this.mContext = c;
	}

	@Override
	protected OverlayItem createItem(int index) {
		return Overlays.get(index);
	}

	@Override
	public int size() {
		return Overlays.size();
	}
	
	public void addOverlay(OverlayItem overlay) {
		Overlays.add(overlay);
		populate();
	}
	
	public void addOverlay(OverlayItem overlay, Drawable marker) {
		overlay.setMarker(boundCenterBottom(marker));
		addOverlay(overlay);
	}
	
	GeoPoint MapCenter;
	public void setCenter(GeoPoint cp){
		this.MapCenter = cp;
	}
	
	@Override
	protected boolean onTap(int index) {
		OverlayItem item = Overlays.get(index);
		final GeoPoint destination = item.getPoint();
		final GeoPoint mapCenter = this.MapCenter;
		
		if (item.getTitle().equals("cp")) return true;

		final String func[] = new String[] { "導航至" + item.getTitle(), "街景圖" };

		AlertDialog.Builder dialog = new AlertDialog.Builder(mContext);
		dialog.setItems(func, new DialogInterface.OnClickListener() {

			public void onClick(DialogInterface dialog, int index) {
				switch (index) {
				case 0:
					String routeUrl = "http://maps.google.com/maps?f=d&saddr="
							+ String.valueOf((mapCenter.getLatitudeE6() / 1E6))	  + ","
							+ String.valueOf((mapCenter.getLongitudeE6() / 1E6))  + "&daddr="
							+ String.valueOf((destination.getLatitudeE6() / 1E6)) + ","
							+ String.valueOf((destination.getLongitudeE6() / 1E6))+ "&hl=tw";
					Uri uri = Uri.parse(routeUrl);
					Intent it = new Intent(Intent.ACTION_VIEW, uri);
					mContext.startActivity(it);
					break;

				case 1:
					String StreetViewMsg = "google.streetview:cbll="
							+ String.valueOf((destination.getLatitudeE6() / 1E6))	+ ","
							+ String.valueOf((destination.getLongitudeE6() / 1E6))	+ "&cbp=1,30,,0,1.0";
					Uri uristreet = Uri.parse(StreetViewMsg);
					Intent itstreet = new Intent(Intent.ACTION_VIEW, uristreet);
					mContext.startActivity(itstreet);
					break;

				}
			}

		});
		dialog.setTitle("定位點功能");
		dialog.show();

		return true;
	}
	
	@Override
	public void draw(Canvas canvas, MapView mapView, boolean shadow) {
		int drawable = 0;
		String message = "";
		
		super.draw(canvas, mapView, shadow);
		
		Projection prj = mapView.getProjection();
		Iterator<OverlayItem> it = Overlays.iterator();
		while (it.hasNext()) {
			   Point screenCoords = new Point();
			   OverlayItem item = it.next();
               prj.toPixels(item.getPoint(), screenCoords);
               
               if (item.getTitle().equals("cp")) {
            	   // 中心地標
            	   drawable = R.drawable.dialog_cp2;
            	   message = item.getSnippet();
               } else {
            	   // 一般地標
            	   drawable = R.drawable.dialog3;
            	   message = item.getTitle() + " (" + item.getSnippet() + ")";
               }
               
               // 畫對話框
               Bitmap shadowIcon = BitmapFactory.decodeResource(mContext.getResources(), drawable);
               canvas.drawBitmap(
            		   shadowIcon,
            		   screenCoords.x, 
            		   screenCoords.y - shadowIcon.getHeight() - 25,null); //screenCoords.y - shadowIcon.getHeight() - 34 
               
               // 畫對話框內的文字
               Paint paint = new Paint();
               paint.setColor(Color.BLACK);
               paint.setStrokeWidth(3);
               paint.setStyle(Paint.Style.FILL);
               paint.setAntiAlias(true);
               canvas.drawText(message, 
            		   screenCoords.x + 15, 
            		   screenCoords.y - 70, paint);
        }
	}
}
