/*
 *  Copyright (c) 2011 The GrinnellPlans Developers
 *  <grinnellplans-development@groups.google.com>
 *  Released under the GPLv3 license. All rights reserved.
 *  
 *  Filename: SessionService.java
 *  Author: Saul St. John
 */

package com.grinnellplans.plandroid;

import org.apache.http.client.protocol.ClientContext;
import org.apache.http.impl.client.BasicCookieStore;
import org.apache.http.protocol.BasicHttpContext;
import org.apache.http.protocol.HttpContext;
import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import android.app.Service;
import android.content.Intent;
import android.os.Binder;
import android.os.IBinder;
import android.util.Log;
import android.widget.Toast;

public class SessionService extends Service {
	private HttpContext _httpContext;
	private boolean _loggedIn;
	private AutofingerList _af;
	private LoginCallback _loginCallback;
	private String _serverName;
	
	public static interface LoginCallback
	{
		public abstract void onLoggedIn(boolean success, String failMessage);
	}
	
	public static interface PlanFetchedCallback
	{
		public abstract void onPlanFetched(boolean success, String plan, String pseudoName);
	}
	
	public SessionService() {
		super();
		_httpContext = new BasicHttpContext();
		_httpContext.setAttribute(ClientContext.COOKIE_STORE, new BasicCookieStore());		
		_loggedIn = false;
		_af = new AutofingerList();
		set_serverName("grinnellplans.com");
	}
	
	public class ServiceBinder extends Binder {
		SessionService getService() {
			return SessionService.this;
		}
	}
	
	private final IBinder _binder = new ServiceBinder();
	
	public void PlanFetch(String planName, PlanFetchedCallback callback)
	{
		new PlanFetchTask(_httpContext, this, callback).execute(planName);
	}
	
	public String get_serverName() {
		return _serverName;
	}

	private void set_serverName(String _serverName) {
		this._serverName = _serverName;
	}
	
	

	public void FetchResult(String resp, PlanFetchedCallback callback)
	{
		StringBuilder plan = new StringBuilder();
		String pseudoName = "";
		JSONObject js = null;
		boolean success = false;
		try {
			js = new JSONObject(resp);
			if(!js.getBoolean("success"))
			{
				plan.append(js.getString("message"));
			}
			else
			{
				success = true;
				js = js.getJSONObject("plandata");
				plan.append(js.getString("plan"));
				pseudoName = js.getString("pseudo");
			}
		}
		catch(JSONException e)
		{
			plan.append(e.getMessage());
		}
		callback.onPlanFetched(success, plan.toString(), pseudoName);
	}
	
	public void TryLogin(String username, String password, LoginCallback loginCallback)
	{
		_loginCallback = loginCallback;
		new LoginTask(_httpContext, this).execute(username, password);
	}
	
	public void LoginResult(String resp)
	{
		Log.i("SessionService::LoginResult","Started");
		JSONObject js = null;
		try {
			Log.i("SessionService::LoginResult","parsing");
			js = new JSONObject(resp);
			if(!js.getBoolean("success"))
			{
				_loginCallback.onLoggedIn(false, js.getString("message"));
			}
			else
			{
				JSONArray af = js.getJSONArray("autofingerList");
				_af.refresh(af);
				_loginCallback.onLoggedIn(true, "");
			}
			
		}
		catch(JSONException je)
		{
			Toast.makeText(this, "Unknown login response: " + resp, Toast.LENGTH_LONG).show();
		}
		Log.i("SessionService::LoginResult","Finished");
	}
	
	public boolean IsLoggedIn()
	{
		return _loggedIn;
	}
	
	public void Logout()
	{
		_httpContext = null;
		_loggedIn = false;
	}
	
	@Override
	public IBinder onBind(Intent intent) {
		return _binder;
	}
	
	@Override
	public int onStartCommand(Intent intent, int flags, int startId)
	{
		return START_STICKY;
	}

	public AutofingerList get_AutofingerList() {
		return _af;
	}
}
