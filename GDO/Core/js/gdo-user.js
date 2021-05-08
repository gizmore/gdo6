"use strict";
var GDO_User = function(json) {
	
	this.JSON = json;
	
	this.authenticated = function() { return this.JSON.user_guest_id <= 0; };
	this.guest = function() { return !this.authenticated(); };
	
	this.id = function(id) { if(id) this.JSON.user_id = id; return this.JSON.user_id; };
	this.secret = function() { return GDT_CONFIG.wss_secret; };
	this.name = function(name) { if(name) this.JSON.user_name = name; return this.JSON.user_name; };
	this.gender = function(gender) { if(gender) this.JSON.user_gender = gender; return this.JSON.user_gender; };
	this.guestName = function(name) { if(name) this.JSON.user_guest_name = name; return this.JSON.user_guest_name; };
	this.realName = function(name) { if(name) this.JSON.user_real_name = name; return this.JSON.user_real_name; };
	this.hasName = function() { return !!this.JSON.user_name; };
	this.hasGuestName = function() { return !!this.JSON.user_guest_name; };
	this.hasRealName = function() { return !!this.JSON.user_real_name; };

	this.level = function() { return this.JSON.user_level; };
	
	this.profileLink = function() {
		return '<a class="gdo-profile-link" title="'+this.displayName()+'" href="'+GDO_WEB_ROOT+'index.php?mo=Profile&amp;me=View&amp;_lang='+GDO_LANGUAGE+'&amp;id='+this.id()+'"><span>'+this.displayName()+'</span></a>';
	};
	
	this.displayName = function() {
		if (this.hasRealName()) {
			return "´" + this.realName() + "´";
		}
		else if (this.hasGuestName()) {
			return "~" + this.guestName() + "~";
		}
		else if (this.hasName()) {
			return this.name();
		}
		else {
			return "~~GHOST~~"; // @TODO getGhostname translation from config.
		}
	};

	this.displayGender = function() { return this.gender() === 'no_gender' ? '' : this.gender(); };
	
	this.update = function(json)
	{
		for (var i in json) {
			if (this.JSON.hasOwnProperty(i)) {
				this.JSON[i] = json[i];
			}
		}
	};

};
