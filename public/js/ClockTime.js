function InitTime(passValue)
	{
		this.DateTime=new String(passValue)
		this.hrNow = this.DateTime.substring(0,2);
		this.mnNow = this.DateTime.substring(3,5);
		this.scNow = this.DateTime.substring(6,9);
		this.ap = this.DateTime.substring(9,11);
		this.DisplayTime=Timer
	}

function Timer(obj)
	{
		this.scNow=eval(this.scNow)+eval(1)
		if (this.scNow==60)
			{
				this.mnNow=eval(this.mnNow)+eval(1)
				this.scNow=0
				if(this.mnNow<10)this.mnNow="0"+this.mnNow
			}

		if (this.mnNow==60)
		{
			this.hrNow=eval(this.hrNow)+eval(1)
			this.mnNow="00"
			if (this.hrNow==12)
				{
					this.ap= ((this.ap=="AM") ? "PM" : "AM")
				}
			if(this.hrNow<10)this.hrNow="0"+this.hrNow
		}
		if (this.hrNow==13)
			{
				this.hrNow="01"		
			}
		
		this.scNow= ((this.scNow<10) ? "0" : "") + this.scNow
			if (this.ap=="")
			{
				obj.value=this.hrNow+":"+this.mnNow+":"+this.scNow
			}
			else
			{
				obj.value=this.hrNow+":"+this.mnNow+":"+this.scNow+" "+this.ap
			}
	}
function InitTime1(passValue)
	{
		this.DateTime=new String(passValue)
		this.hrNow = this.DateTime.substring(0,2);
		this.mnNow = this.DateTime.substring(3,5);
		this.scNow = this.DateTime.substring(6,9);
		this.ap = this.DateTime.substring(9,11);
		this.DisplayTime=TimerTimer
	}

function TimerTimer(obj)
	{
		this.scNow=eval(this.scNow)+eval(1)
		if (this.scNow==60)
			{
				this.mnNow=eval(this.mnNow)+eval(1)
				this.scNow=0
				if(this.mnNow<10)this.mnNow="0"+this.mnNow
			}

		if (this.mnNow==60)
		{
			this.hrNow=eval(this.hrNow)+eval(1)
			this.mnNow="00"
			if (this.hrNow==12)
				{
					this.ap= ((this.ap=="AM") ? "PM" : "AM")
				}
			if(this.hrNow<10)this.hrNow="0"+this.hrNow
		}
		if (this.hrNow==13)
			{
				this.hrNow="01"		
			}
		
		this.scNow= ((this.scNow<10) ? "0" : "") + this.scNow
			if (this.ap=="")
			{
				obj.innerHTML=this.hrNow+":"+this.mnNow+":"+this.scNow
			}
			else
			{
				obj.innerHTML=this.hrNow+":"+this.mnNow+":"+this.scNow+" "+this.ap
			}
	}