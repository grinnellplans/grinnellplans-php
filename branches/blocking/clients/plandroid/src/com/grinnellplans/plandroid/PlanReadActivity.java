/*
 *  Copyright (c) 2011 The GrinnellPlans Developers
 *  <grinnellplans-development@groups.google.com>
 *  Released under the GPLv3 license. All rights reserved.
 *  
 *  Filename: PlanReadActivity.java
 *  Author: Saul St. John
 */

package com.grinnellplans.plandroid;

import android.app.Activity;
import android.app.ProgressDialog;
import android.content.ComponentName;
import android.content.Context;
import android.content.Intent;
import android.content.ServiceConnection;
import android.net.Uri;
import android.os.Bundle;
import android.os.IBinder;
import android.util.Log;
import android.webkit.WebView;
import android.widget.Toast;

public class PlanReadActivity extends Activity implements ServiceConnection, SessionService.PlanFetchedCallback {
	private SessionService mSessionService;
	private WebView mWebView;
	private String mPlanName;
	private ProgressDialog mPlanLoadingDialog;
	
	public void onServiceConnected(ComponentName className, IBinder service) {
		mSessionService = ((SessionService.ServiceBinder)service).getService();
		mSessionService.PlanFetch(mPlanName, this);
	}

	public void onServiceDisconnected(ComponentName className) {
		mSessionService = null;
	}
	
	@Override
	public void onCreate(Bundle savedInstanceState)
	{
		super.onCreate(savedInstanceState);
		final Uri uri;
		uri = getIntent().getData();
		mPlanName = uri.getLastPathSegment();
		if(null != mPlanName)
		{
			mPlanLoadingDialog = new ProgressDialog(this);
			mPlanLoadingDialog.setMessage("Loading [" + mPlanName + "]...");
			mPlanLoadingDialog.setProgressStyle(ProgressDialog.STYLE_SPINNER);
			mPlanLoadingDialog.show();
			
			mWebView = new WebView(this);
			setContentView(mWebView);
			if(!bindService(new Intent(this, SessionService.class), this, Context.BIND_AUTO_CREATE))
				Log.e("PlanReadActivity::onCreate", "Couldn't bind SessionService");
		}
		else
		{
			finish();
		}
	}
	
	@Override
	public void onDestroy()
	{
		if(null != mSessionService)
			unbindService(this);
		super.onDestroy();
	}
	
	private void setPlanTitle(String planName, String pseudoName, String lastUpdated, String lastLogin)
	{
		setTitle("[" + planName + "] - " + pseudoName);
	}

	public void onPlanFetched(boolean success, String plan, String pseudoName) {
		if(success)
		{
			setPlanTitle(mPlanName, pseudoName, "0-0-0", "0-0-0");
			mWebView.loadDataWithBaseURL("plans://" + mSessionService.get_serverName(), plan, "text/html", "UTF-8", "");
			mSessionService.get_AutofingerList().removeUser(mPlanName);
		}
		else
		{
			Toast.makeText(this, plan, Toast.LENGTH_SHORT).show();
			finish();
		}
		mPlanLoadingDialog.dismiss();
	}
}
