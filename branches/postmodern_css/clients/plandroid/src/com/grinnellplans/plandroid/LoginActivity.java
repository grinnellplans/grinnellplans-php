/*
 *  Copyright (c) 2011 The GrinnellPlans Developers
 *  <grinnellplans-development@groups.google.com>
 *  Released under the GPLv3 license. All rights reserved.
 *  
 *  Filename: LoginActivity.java
 *  Author: Saul St. John
 */

package com.grinnellplans.plandroid;

import com.grinnellplans.plandroid.R;

import android.app.Activity;
import android.app.ProgressDialog;
import android.content.ComponentName;
import android.content.Context;
import android.content.Intent;
import android.content.ServiceConnection;
import android.os.Bundle;
import android.os.IBinder;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.EditText;
import android.widget.Button;
import android.widget.Toast;


public class LoginActivity extends Activity implements OnClickListener, ServiceConnection, SessionService.LoginCallback {
	private SessionService mSessionService;
	private ProgressDialog mLoggingInDialog;
	
		public void onServiceConnected(ComponentName className, IBinder service) 
		{
			mSessionService = ((SessionService.ServiceBinder)service).getService();
		}
		public void onServiceDisconnected(ComponentName className)
		{
			mSessionService = null;
		}
	
		public void onClick(View arg0) {
			EditText un = (EditText) findViewById(R.id.Username);
			EditText pw = (EditText) findViewById(R.id.Password);
			mSessionService.TryLogin(un.getText().toString(), pw.getText().toString(), this);
			mLoggingInDialog = new ProgressDialog(this);
			mLoggingInDialog.setProgressStyle(ProgressDialog.STYLE_SPINNER);
			mLoggingInDialog.setMessage("Logging in...");
			mLoggingInDialog.show();
		}
	
	@Override
	public void onCreate(Bundle savedInstanceState)
	{
		super.onCreate(savedInstanceState);
		bindService(new Intent(LoginActivity.this, SessionService.class), this, Context.BIND_AUTO_CREATE);
		setContentView(R.layout.login);
		Button btn = (Button) findViewById(R.id.Login);
		btn.setOnClickListener(this);
		setResult(RESULT_CANCELED);
	}

	@Override
	public void onDestroy()
	{
		if(null != mSessionService)
			unbindService(this);
		super.onDestroy();
	}
	
	public void onLoggedIn(boolean success, String failMessage) {
		mLoggingInDialog.dismiss();
		if(success)
		{
			setResult(RESULT_OK);
			finish();
		}
		else
		{
			Toast.makeText(this, failMessage, Toast.LENGTH_LONG).show();
		}
	}
}
