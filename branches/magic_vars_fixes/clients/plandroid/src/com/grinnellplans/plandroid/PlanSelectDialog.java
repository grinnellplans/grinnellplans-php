/*
 *  Copyright (c) 2011 The GrinnellPlans Developers
 *  <grinnellplans-development@groups.google.com>
 *  Released under the GPLv3 license. All rights reserved.
 *  
 *  Filename: PlanSelectDialog.java
 *  Author: Saul St. John
 */

package com.grinnellplans.plandroid;

import android.app.AlertDialog;
import android.content.Context;
import android.content.DialogInterface;
import android.widget.EditText;
import android.content.DialogInterface.OnClickListener;

public class PlanSelectDialog extends AlertDialog implements OnClickListener {
	private EditText mET;
	private OnPlanSelectListener _cb;
	
	public interface OnPlanSelectListener
	{
		public abstract void onPlanSelected(String selectedPlan);
	}

	public PlanSelectDialog(Context context, OnPlanSelectListener cb) {
		super(context);
		_cb = cb;
		mET = new EditText(context);
		mET.setSingleLine(true);
		this.setMessage("Select plan:");
		this.setView(mET);
		this.setCancelable(true);
		this.setButton(BUTTON_POSITIVE, "OK", this);
		this.setButton(BUTTON_NEGATIVE, "Cancel", this);
	}

	public void onClick(DialogInterface dialog, int which) {
		dismiss();
		if(BUTTON_POSITIVE == which)
			_cb.onPlanSelected(mET.getText().toString());
	}
}
