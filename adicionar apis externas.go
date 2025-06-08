adicionar apis externas 

bf197fb6eee496862ee45187fbe8cd78

get
https://api.hetrixtools.com/v1/bf197fb6eee496862ee45187fbe8cd78/blacklist/ipv4/rbls/

response 
[
	{
		"RBL": "0spam.fusionzero.com"
	},
	{
		"RBL": "access.redhawk.org"
	},
	{
		"RBL": "all.s5h.net"
	},
	{
		"RBL": "all.spamrats.com"
	},
	{
		"RBL": "b.barracudacentral.org"
	},
	{
		"RBL": "backscatter.spameatingmonkey.net"
	},
	{
		"RBL": "bb.barracudacentral.org"
	},
	{
		"RBL": "bl.drmx.org"
	},
	{
		"RBL": "bl.mailspike.net"
	},
	{
		"RBL": "bl.nosolicitado.org"
	},
	{
		"RBL": "bl.rbl.scrolloutf1.com"
	},
	{
		"RBL": "bl.scientificspam.net"
	},
	{
		"RBL": "bl.spamcop.net"
	},
	{
		"RBL": "bl.spameatingmonkey.net"
	},
	{
		"RBL": "bl.suomispam.net"
	},
	{
		"RBL": "bl.worst.nosolicitado.org"
	},
	{
		"RBL": "black.junkemailfilter.com"
	},
	{
		"RBL": "black.mail.abusix.zone"
	},
	{
		"RBL": "cart00ney.surriel.com"
	},
	{
		"RBL": "cbl.abuseat.org"
	},
	{
		"RBL": "dnsbl-1.uceprotect.net"
	},
	{
		"RBL": "dnsbl-2.uceprotect.net"
	},
	{
		"RBL": "dnsbl-3.uceprotect.net"
	},
	{
		"RBL": "dnsbl.dronebl.org"
	},
	{
		"RBL": "dnsbl.justspam.org"
	},
	{
		"RBL": "dnsbl.kempt.net"
	},
	{
		"RBL": "dnsbl.net.ua"
	},
	{
		"RBL": "dnsbl.spfbl.net"
	},
	{
		"RBL": "dnsbl.tornevall.org"
	},
	{
		"RBL": "dnsbl.zapbl.net"
	},
	{
		"RBL": "dnsrbl.swinog.ch"
	},
	{
		"RBL": "dyna.spamrats.com"
	},
	{
		"RBL": "exploit.mail.abusix.zone"
	},
	{
		"RBL": "fnrbl.fast.net"
	},
	{
		"RBL": "hostkarma.junkemailfilter.com"
	},
	{
		"RBL": "invaluement SIP"
	},
	{
		"RBL": "invaluement SIP/24"
	},
	{
		"RBL": "NordSpam"
	},
	{
		"RBL": "ips.backscatterer.org"
	},
	{
		"RBL": "mail-abuse.blacklist.jippg.org"
	},
	{
		"RBL": "multi.surbl.org"
	},
	{
		"RBL": "netscan.rbl.blockedservers.com"
	},
	{
		"RBL": "noptr.spamrats.com"
	},
	{
		"RBL": "pbl.spamhaus.org"
	},
	{
		"RBL": "psbl.surriel.com"
	},
	{
		"RBL": "rbl.abuse.ro"
	},
	{
		"RBL": "rbl.blockedservers.com"
	},
	{
		"RBL": "rbl.dns-servicios.com"
	},
	{
		"RBL": "rbl.interserver.net"
	},
	{
		"RBL": "rbl2.triumf.ca"
	},
	{
		"RBL": "rep.mailspike.net"
	},
	{
		"RBL": "sbl.spamhaus.org"
	},
	{
		"RBL": "spam.dnsbl.anonmails.de"
	},
	{
		"RBL": "spam.pedantic.org"
	},
	{
		"RBL": "spam.rbl.blockedservers.com"
	},
	{
		"RBL": "spam.spamrats.com"
	},
	{
		"RBL": "spamlist.or.kr"
	},
	{
		"RBL": "spamrbl.imp.ch"
	},
	{
		"RBL": "spamsources.fabel.dk"
	},
	{
		"RBL": "mail-abuse.com"
	},
	{
		"RBL": "talosintelligence.com"
	},
	{
		"RBL": "torexit.dan.me.uk"
	},
	{
		"RBL": "truncate.gbudb.net"
	},
	{
		"RBL": "xbl.spamhaus.org"
	},
	{
		"RBL": "z.mailspike.net"
	},
	{
		"RBL": "zen.spamhaus.org"
	}
]


verificar blacklist ipv4
get
https://api.hetrixtools.com/v2/bf197fb6eee496862ee45187fbe8cd78/blacklist-check/ipv4/1.1.1.1/

{
	"status": "SUCCESS",
	"api_calls_left": 2000,
	"blacklist_check_credits_left": 99,
	"blacklisted_count": 0,
	"blacklisted_on": [
	  {
		"rbl": null,
		"delist": null
	  }
	],
	"links": {
	  "report_link": "https://hetrixtools.com/report/blacklist/8820ddf10c0da3e74c7e587dd0b17bc7/",
	  "whitelabel_report_link": "",
	  "api_report_link": "https://api.hetrixtools.com/v1/bf197fb6eee496862ee45187fbe8cd78/blacklist/report/5.230.55.154/",
	  "api_blacklist_check_link": "https://api.hetrixtools.com/v2/bf197fb6eee496862ee45187fbe8cd78/blacklist-check/ipv4/5.230.55.154/"
	}
  }

  verificar dominio 
  https://api.hetrixtools.com/v2/bf197fb6eee496862ee45187fbe8cd78/blacklist-check/domain/app.acessochaveprime.com/
  https://api.hetrixtools.com/v2/bf197fb6eee496862ee45187fbe8cd78/blacklist-check/domain/app.acessochaveprime.com/









  hwmkdjjhipky.7.1.1.127.dnsbl.httpbl.org



API VALIDAÇAO DE TELEOFNE 
  https://secondline.com/asYouType?phoneNumber=%2B5531986491213&countryCodeIso=br
  {
    "countryCodeIso": "BR",
    "originalInput": "+5531986491213",
    "asYouType": "+55 31 98649-1213",
    "international": "+55 31 98649-1213",
    "smsCapable": true,
    "dialable": true,
    "errorText": "",
    "location": "Minas Gerais, Brasil",
    "timezone": "America/Sao_Paulo",
    "endpointId": "5531986491213",
    "e164Destination": "BR-MOBILE",
    "info": {
        "validationResult": "VALID",
        "googlePhoneType": "MOBILE",
        "locationGoogle": "Minas Gerais",
        "locationNumberingPlan": "Minas Gerais, Brasil",
        "carrierGoogle": "Oi",
        "carrierNumberingPlan": "TNL PCS S.A."
    }
}