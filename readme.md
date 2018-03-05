## About Site work.

#### Status of Site

* If user using facebook button in Site, it will authorize user with app, create or update this Facebook account and set 

```textmate
  $fbAccount = Account::updateOrCreate([
                     'social_id' => $fbUser['id'],
                     'social_type' => config('system.social_type.facebook')
                 ], [
                     'social_name' => $fbUser['name'],
                     'api_token' => (string) $accessTokenLong,
                     'api_token_start_date' => Carbon::now()->toDateTimeString(),
                     'user_id' => $user->id,
                     'status' => true,
                 ]);
```

* All the Ads Accounts already related to this Facebook will be active again :

```textmate
Content::where('account_id', $fbAccount->id)->update(['status' => true]);
```

* After that we stores all the Ads Accounts it have in TempTable :

```textmate
            TempAdAccount::where('account_id', $fbAccount->id)->delete();
                foreach ($accounts as $account) {
                    TempAdAccount::create([
                        'social_id' => $account->account_id,
                        'social_type' => config('system.social_type.facebook'),
                        'account_id' => $fbAccount->id,
                        'social_name' => $account->name,
                        'currency' => $account->currency
                    ]);
                }
```

* In next Step, the popup will open to let user choose which Ads Account to map with this user.

```textmate
#first release all existed AdAccounts with this user 
           Content::where('user_id', $user->id)->update([
                'user_id' => null
            ]);

            $tempAdAccounts = TempAdAccount::whereIn('id', $request->get('status'))
                ->get();
            # Second update all ads accounts which choose from popup to this user.
            foreach ($tempAdAccounts as $tempAdAccount) {

               $adAccount = Content::updateOrCreate([
                    'social_id' => $tempAdAccount->social_id,
                    'social_type' => config('system.social_type.facebook')
                ], [
                    'social_name' => $tempAdAccount->social_name,
                    'currency' => $tempAdAccount->currency,
                    'account_id' => $tempAdAccount->account_id,
                    'user_id' => $user->id,
                    'status' => true
                ]);
               #fectch the insight for first time.
               Helpers::fetchAccountElements($adAccount);
            }
```
#### Cronjob Get elements.

```textmate
$adAccounts = Content::whereNotNull('user_id')
            ->where('social_type', config('system.social_type.facebook'))
            ->where('status', true)
            ->get();
            
               
```
```textmate
 foreach ($adAccounts as $adAccount) {
            Helpers::fetchAccountElements($adAccount);
        }
```
When fetch element if get authorize then accounts and contents related be update to status false and can not fetch again until user with account related to make account and content active again.


* Date Insight CronJob We only fetch Insight and Log errors.


#### Admin function map ad account to user 

Only can assign ads account which not belong to any user.

Please notice that one contents can be in many facebook account, so each time latest account authorize will own this content.

* When create user we no need to setup Ad Accounts

* When edit User :

We list all Ads Account which from User Facebook Accounts


```textmate
        $accountIds = Account::where('user_id', $user->id)->pluck('id')->all();
        $contents = Content::whereIn('account_id', $accountIds)->get();


        foreach ($contents as $content) {
            $data[$content->id] = $content->social_name;

            if ($content->user_id) {
                $data[$content->id] .= ' (Owned by user '.$content->user->name.")";
            }
        }

       
```

After that when admin assign we will create:

```textmate
        if ($this->filled('contents')) {
            Content::where('user_id', $user->id)->update(['user_id' => null]);
            Content::whereIn('id', $this->get('contents'))->update(['user_id' => $user->id]);
        } else {
            Content::where('user_id', $user->id)->update(['user_id' => null]);
        }
```

* For reporting, we listed all reports for elements which have user_id map for ad accounts in both (Report Page and Index Page)

For some accounts which not assign user yet, we still get insight for this but will not count in Report.