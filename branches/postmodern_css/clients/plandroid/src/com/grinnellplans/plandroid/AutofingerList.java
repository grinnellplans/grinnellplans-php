/*
 *  Copyright (c) 2011 The GrinnellPlans Developers
 *  <grinnellplans-development@groups.google.com>
 *  Released under the GPLv3 license. All rights reserved.
 *  
 *  Filename: AutofingerList.java
 *  Author: Saul St. John
 */

package com.grinnellplans.plandroid;

import java.util.ArrayList;
import java.util.Hashtable;
import java.util.List;
import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import android.database.DataSetObservable;

public class AutofingerList extends DataSetObservable {
	private static final int AUTOFINGER_LEVELS = 3;
	private Hashtable<String, List<String>> _userXlevel;
	private ArrayList<ArrayList<String>> _theList;
	public AutofingerList()
	{
		_theList = new ArrayList<ArrayList<String>>(AUTOFINGER_LEVELS);
		for(int i = 0; i < AUTOFINGER_LEVELS; i++)
			_theList.add(new ArrayList<String>());
				
		_userXlevel = new Hashtable<String, List<String>>();
	}

	public void refresh(JSONArray af) throws JSONException
	{
		for(int i = 0; i < AUTOFINGER_LEVELS; i++)
			_theList.get(i).clear();
		for(int i = 0; i < af.length(); i++)
		{
			JSONObject afl = af.getJSONObject(i);
			int level = afl.getInt("level");
			JSONArray users = afl.getJSONArray("usernames");
			for(int j = 0; j < users.length(); j++)
				addUser(level - 1, users.getString(j));
		}
		notifyInvalidated();
	}
	
	public void addUser(int level, String user)
	{
		if(_userXlevel.containsKey(user))
			if(_userXlevel.get(user).equals(_theList.get(level)))
				return;
			else
				removeUser(user);
		_theList.get(level).add(user);
		_userXlevel.put(user, _theList.get(level));
		notifyChanged();
	}
	
	public void removeUser(String user)
	{
		List<String> level = _userXlevel.get(user);
		if(null != level)
		{
			level.remove(user);
			_userXlevel.remove(user);
			notifyChanged();
		}
	}
	
	public ArrayList<String> get(int index)
	{
		return _theList.get(index);
	}
	
	public int size()
	{
		return AUTOFINGER_LEVELS;
	}
}
