/*  
 *  Copyright (c) 2011 The GrinnellPlans Developers 
 *  <grinnellplans-development@groups.google.com>
 *  Released under the GPLv3 license. All rights reserved.
 *  
 *  Filename: AutofingerActivity.java
 *  Author: Saul St. John
 */

package com.grinnellplans.plandroid;

import com.grinnellplans.plandroid.PlanSelectDialog.OnPlanSelectListener;
import android.app.ExpandableListActivity;
import android.content.ComponentName;
import android.content.Context;
import android.content.Intent;
import android.content.ServiceConnection;
import android.database.DataSetObserver;
import android.net.Uri;
import android.os.Bundle;
import android.os.IBinder;
import android.util.Log;
import android.view.Gravity;
import android.view.Menu;
import android.view.MenuInflater;
import android.view.MenuItem;
import android.view.View;
import android.view.ViewGroup;
import android.widget.AbsListView;
import android.widget.BaseExpandableListAdapter;
import android.widget.ExpandableListView;
import android.widget.ExpandableListView.OnChildClickListener;
import android.widget.TextView;

public class AutofingerActivity extends ExpandableListActivity implements OnChildClickListener, OnPlanSelectListener, ServiceConnection {
	private SessionService mSessionService;
	private AutofingerList mAutofingerList;

	public void onServiceConnected(ComponentName className, IBinder service) 
	{
		mSessionService = ((SessionService.ServiceBinder)service).getService();
		mAutofingerList = mSessionService.get_AutofingerList();
		setListAdapter(new AutofingerExpandableListAdapter(mAutofingerList));
	}
	
	public void onServiceDisconnected(ComponentName className)
	{
		mSessionService = null;
	}
	
	public boolean onChildClick(ExpandableListView parent, View v,
								int groupPosition, int childPosition, long id) 
	{		
			readPlan(mAutofingerList.get(groupPosition).get(childPosition));
			return true;
	}
	
	private void readPlan(String plan)
	{
		Intent readPlanIntent = 
			new Intent(	Intent.ACTION_VIEW, 
						new Uri.Builder()
							.scheme("plans")
							.authority(mSessionService.get_serverName())
							.appendPath("plan")
							.appendPath(plan)
							.build());
		startActivity(readPlanIntent);
		
	}
	
	@Override
	public void onCreate(Bundle savedInstanceState)
	{
		super.onCreate(savedInstanceState);
		this.getExpandableListView().setOnChildClickListener((OnChildClickListener) this);
		if(!bindService(new Intent(this, SessionService.class), this, Context.BIND_AUTO_CREATE))
			Log.e("AutofingerActivity::onCreate", "Couldn't bind SessionService");
	}
	
	@Override
	public void onDestroy()
	{
		if(null != mSessionService)
			unbindService(this);
		super.onDestroy();
	}
	
	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
	    MenuInflater inflater = getMenuInflater();
	    inflater.inflate(R.menu.af_menu, menu);
	    return true;
	}
	
	@Override
	public boolean onOptionsItemSelected(MenuItem item) {
		switch (item.getItemId())
		{
		case R.id.logout:
			mSessionService.Logout();
			finish();
			return true;
		case R.id.read:
			new PlanSelectDialog(this, this).show();
		}
		return super.onOptionsItemSelected(item);
	}
	
	public class AutofingerExpandableListAdapter extends BaseExpandableListAdapter {
		private AutofingerList _af;
		
		public AutofingerExpandableListAdapter(AutofingerList af)
		{
			_af = af;
			af.registerObserver(new DataSetObserver() {

				@Override
				public void onChanged() {
					super.onChanged();
					AutofingerExpandableListAdapter.this.notifyDataSetChanged();
				}
				
				@Override
				public void onInvalidated() {
					super.onInvalidated();
					AutofingerExpandableListAdapter.this.notifyDataSetInvalidated();
				}
				
			});
		}
		
        public Object getChild(int groupPosition, int childPosition) {
            return _af.get(groupPosition).get(childPosition);
        }

        public long getChildId(int groupPosition, int childPosition) {
            return childPosition;
        }

        public int getChildrenCount(int groupPosition) {
            return _af.get(groupPosition).size();
        }

       public TextView getGenericView() {
           TextView textView = new TextView(AutofingerActivity.this);
            // Layout parameters for the ExpandableListView
            AbsListView.LayoutParams lp = new AbsListView.LayoutParams(
                    ViewGroup.LayoutParams.MATCH_PARENT, 64);

            textView.setLayoutParams(lp);
            // Center the text vertically
            textView.setGravity(Gravity.CENTER_VERTICAL | Gravity.LEFT);
            // Set the text starting position
            textView.setPadding(36, 0, 0, 0);
            return textView;
        }

        public View getChildView(int groupPosition, int childPosition, boolean isLastChild,
                View convertView, ViewGroup parent) {
            TextView textView = getGenericView();
            textView.setText(getChild(groupPosition, childPosition).toString());
            return textView; //textView;
        }

        public Object getGroup(int groupPosition) {
            return _af.get(groupPosition); //groups[groupPosition];
        }

        public int getGroupCount() {
            return _af.size();
        }

        public long getGroupId(int groupPosition) {
            return groupPosition;
        }

        public View getGroupView(int groupPosition, boolean isExpanded, View convertView,
                ViewGroup parent) {
        	TextView textView = getGenericView();
            textView.setText("Level " + (1 + groupPosition) + (_af.get(groupPosition).size() > 0 ? " [" + _af.get(groupPosition).size() + "]" : "" ));
            return textView; //textView;
        }

        public boolean isChildSelectable(int groupPosition, int childPosition) {
            return true;
        }

        public boolean hasStableIds() {
            return false;
        }
    }

	public void onPlanSelected(String selectedPlan) {
		readPlan(selectedPlan);
	}
}
