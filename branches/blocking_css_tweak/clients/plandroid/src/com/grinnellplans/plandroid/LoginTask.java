/*
 *  Copyright (c) 2011 The GrinnellPlans Developers
 *  <grinnellplans-development@groups.google.com>
 *  Released under the GPLv3 license. All rights reserved.
 *  
 *  Filename: LoginTask.java
 *  Author: Saul St. John
 */

package com.grinnellplans.plandroid;

import java.io.BufferedReader;
import java.io.InputStreamReader;
import java.util.ArrayList;
import java.util.List;

import org.apache.http.HttpResponse;
import org.apache.http.NameValuePair;
import org.apache.http.client.entity.UrlEncodedFormEntity;
import org.apache.http.client.methods.HttpPost;
import org.apache.http.message.BasicNameValuePair;
import org.apache.http.protocol.HttpContext;
import org.json.JSONObject;

import android.net.http.AndroidHttpClient;
import android.os.AsyncTask;
import android.util.Log;

public class LoginTask extends AsyncTask<String, Void, String> {
	private HttpContext _httpContext;
	private SessionService _ss;
	
	public LoginTask(HttpContext clientContext, SessionService sessionService)
	{
		_httpContext = clientContext;
		_ss = sessionService;
	}
	
	protected String doInBackground(String... params) {
		Log.i("LoginTask::doInBackground", "entered");
		AndroidHttpClient plansClient = AndroidHttpClient.newInstance("plandroid");
		final HttpPost req = new HttpPost("http://" + _ss.get_serverName() + "/api/1/index.php?task=login");
		List<NameValuePair> postParams = new ArrayList<NameValuePair>(2);
		postParams.add(new BasicNameValuePair("username", params[0]));
		postParams.add(new BasicNameValuePair("password", params[1]));
		Log.i("LoginTask::doInBackground", "setting postParams");
		String resp = null;
		try {
			req.setEntity(new UrlEncodedFormEntity(postParams));
			HttpResponse response = null;
			Log.i("LoginTask::doInBackground", "executing request");
			response = plansClient.execute(req, _httpContext);
			Log.i("LoginTask::doInBackground", "reading response");
			resp = new BufferedReader((new InputStreamReader(response.getEntity().getContent()))).readLine();
		} 
		catch(Exception e)
		{
			resp = constructResponse(e);
		}
		plansClient.close();
		Log.i("LoginTask::doInBackground", "server responded \"" + resp + "\"");
		Log.i("LoginTask::doInBackground", "exit");
		return resp;
	}
	
	private String constructResponse(Exception e)
	{
		Log.w("LoginTask::constructResponse", "enter");
		String resp = null;
		try
		{
			JSONObject respObject = new JSONObject();
			respObject.put("success", false);
			respObject.put("message", new StringBuilder().append(e.getClass().getName()).append(": ").append(e.getMessage()).toString());
			
			resp = respObject.toString();
		}
		catch(Exception localException)
		{
			Log.w("LoginTask::constructResponse", "caught exception");
			resp = "{\"success\":false,\"message\":\"unknown local exception occured\"}";
		}
		Log.w("LoginTask::constructResponse", "exit");
		return resp;
	}
	
	protected void onPostExecute(String resp)
	{
		Log.i("LoginTask::onPostExecute", "Started");
		_ss.LoginResult(resp);
		Log.i("LoginTask::onPostExecute", "Finished");
	}
}
