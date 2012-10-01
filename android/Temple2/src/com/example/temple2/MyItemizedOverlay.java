package com.example.temple2;

import java.util.ArrayList;

import android.app.AlertDialog;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.graphics.drawable.Drawable;
import android.net.Uri;

import com.google.android.maps.GeoPoint;
import com.google.android.maps.ItemizedOverlay;
import com.google.android.maps.OverlayItem;

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
	
	@Override
	protected boolean onTap(int index) {
		OverlayItem item = Overlays.get(index);
		final GeoPoint destination = item.getPoint();
		final GeoPoint mapCenter = this.getCenter();

		final String func[] = new String[] { "導航至此", "街景圖" };

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
}
