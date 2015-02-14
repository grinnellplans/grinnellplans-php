/*
 *  Copyright (c) 2011 The GrinnellPlans Developers
 *  <grinnellplans-development@groups.google.com>
 *  Released under the GPLv3 license. All rights reserved.
 *  
 *  Filename: PlanFetchTask.java
 *  Author: Saul St. John
 */

package com.grinnellplans.plandroid;

import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStreamReader;
import java.io.UnsupportedEncodingException;
import java.util.ArrayList;
import java.util.List;

import org.apache.http.HttpResponse;
import org.apache.http.NameValuePair;
import org.apache.http.client.entity.UrlEncodedFormEntity;
import org.apache.http.client.methods.HttpPost;
import org.apache.http.message.BasicNameValuePair;
import org.apache.http.protocol.HttpContext;

import android.net.http.AndroidHttpClient;
import android.os.AsyncTask;
import android.util.Log;

public class PlanFetchTask extends AsyncTask<String, Void, String> {
	private HttpContext _httpContext;
	private SessionService _ss;
	private SessionService.PlanFetchedCallback _callback;
	
	public PlanFetchTask(HttpContext clientContext, SessionService sessionService, SessionService.PlanFetchedCallback callback)
	{
		_httpContext = clientContext;
		_ss = sessionService;
		_callback = callback;
	}
	
	protected String doInBackground(String... params) {
		Log.i("PlanFetchTask::doInBackground", "Started");
		AndroidHttpClient plansClient = AndroidHttpClient.newInstance("plandroid");
		
		final HttpPost req = new HttpPost("http://" + _ss.get_serverName() + "/api/1/index.php?task=read");
		List<NameValuePair> postParams = new ArrayList<NameValuePair>(1);
		postParams.add(new BasicNameValuePair("username", params[0]));
		postParams.add(new BasicNameValuePair("readlinkreplacement", "/plan/{username}"));
		Log.i("PlanFetchTask::doInBackground", "setting postParams");
		try {
			req.setEntity(new UrlEncodedFormEntity(postParams));
		} catch (UnsupportedEncodingException e1) {
			// TODO Auto-generated catch block
			e1.printStackTrace();
		}
		HttpResponse response = null;
		try {
			Log.i("PlanFetchTask::doInBackground", "executing request");
			response = plansClient.execute(req, _httpContext);
		} catch (IOException e) {
			e.printStackTrace();
		}
		String resp = null;
		try {
			Log.i("PlanFetchTask::doInBackground", "reading response");
			resp = new BufferedReader((new InputStreamReader(response.getEntity().getContent()))).readLine();
		} catch (IllegalStateException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		} catch (IOException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
		plansClient.close();
		Log.i("PlanFetchTask::doInBackground", "server responded \"" + resp + "\"");
		return resp;
	}

	protected void onPostExecute(String resp)
	{
		_ss.FetchResult(resp, _callback);
	}
}
