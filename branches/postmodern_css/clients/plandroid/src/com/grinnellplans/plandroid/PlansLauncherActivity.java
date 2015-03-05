/*
 *  Copyright (c) 2011 The GrinnellPlans Developers
 *  <grinnellplans-development@groups.google.com>
 *  Released under the GPLv3 license. All rights reserved.
 *  
 *  Filename: PlansLauncherActivity.java
 *  Author: Saul St. John
 */

package com.grinnellplans.plandroid;

import android.app.Activity;
import android.content.ComponentName;
import android.content.Context;
import android.content.Intent;
import android.content.ServiceConnection;
import android.os.Bundle;
import android.os.IBinder;

public class PlansLauncherActivity extends Activity implements ServiceConnection {
	private SessionService mSessionService;
	
	public void onServiceConnected(ComponentName className, IBinder service) 
	{
		mSessionService = ((SessionService.ServiceBinder)service).getService();
		if(!mSessionService.IsLoggedIn())
		{
			Intent loginIntent = new Intent("com.grinnellplans.plandroid.LOGIN");
			startActivityForResult(loginIntent, 0);
		} 
		else
		{
			Intent autoFingerIntent = new Intent("com.grinnellplans.plandroid.AUTOFINGER");
			startActivity(autoFingerIntent);
			PlansLauncherActivity.this.finish();
		}	
	}
		
	public void onServiceDisconnected(ComponentName className)
	{
		mSessionService = null;
	}
	
	@Override 
	protected void onActivityResult(int requestCode, int resultCode, Intent data)
	{
		if(RESULT_OK == resultCode)
		{
			Intent autoFingerIntent = new Intent("com.grinnellplans.plandroid.AUTOFINGER");
			startActivity(autoFingerIntent);
		}
		finish();
	}
	
	@Override
	public void onCreate(Bundle savedInstanceState)
	{
		super.onCreate(savedInstanceState);
		startService(new Intent(this, SessionService.class));
		bindService(new Intent(this, SessionService.class), this, Context.BIND_AUTO_CREATE);
	}
	
	@Override
	public void onDestroy()
	{
		if(null != mSessionService)
		{
			boolean shouldStopService = !mSessionService.IsLoggedIn();
			unbindService(this);
			if(shouldStopService)
				stopService(new Intent(this, SessionService.class));
		}
		super.onDestroy();
	}
}
