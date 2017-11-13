#### About Facebook Ads

API is wrapper of Graph API.

Each Campaign may contains many Ads and AdSets.

Check code Example at `https://developers.facebook.com/docs/marketing-api/reference/ad-campaign-group`

* Read on `https://developers.facebook.com/docs/marketing-api/insights` for all about Insight Params and Fields.

* Read `https://developers.facebook.com/docs/marketing-api/reference/sdks/python/ad-account/v2.11` about full document about API.

* Debug Access Token about its expired time `https://developers.facebook.com/tools/debug/accesstoken`

* Located about Making API call `vendor\facebook\php-ads-sdk\src\FacebookAds\ApiRequest.php`

* Fields of Insight `vendor\facebook\php-ads-sdk\src\FacebookAds\Object\Fields\AdsInsightsFields.php`

* Path for Ads Class `D:\docker\html\open\vendor\facebook\php-ads-sdk\src\FacebookAds\Object\Ad.php`

* How to break Insight by Date
`/insights?fields=impressions,social_clicks,website_clicks,ctr&time_range={'since':'2015-01-01','until':'2015-01-30'}&time_increment=1`

Read more at `https://developers.facebook.com/docs/marketing-api/reference/ad-account/insights/`

* About User need to be Admin of App in Order using Ads API

We need to add user as admin of App at `https://developers.facebook.com/apps/234907703926/roles/`


```text



This is because the objects you can promote are not based upon an adaccount but are based upon your user.

You can see the connection objects a user has access to by make the following request:

<API_VERSION>/act_<AD_ACCOUNT_ID>/connectionobjects?access_token=<ACCESS_TOKEN>

The documentation states:

    this call returns the IDs of all objects for which the current session user is an administrator, and the IDs of apps for which the user is listed as a developer or advertiser.




If you don't want to make additional administrators for your app, you can create system user in Facebook Business Manager, grant him administrator (or even advertiser) rights for Ad Account.

https://developers.facebook.com/docs/marketing-api/business-manager-api

And use his token to manage ads. In my case I used it to retrieve targeting of ads, to find where leads came to lead form from.

https://www.facebook.com/marketingdevelopers/videos/vb.606699326111137/883648801749520/
```

