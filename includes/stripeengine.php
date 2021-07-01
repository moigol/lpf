<?php
/**
 * Stripe Object
 *
 * @category   Helper
 * @package    Stripe
 * @author     Mo <moises.goloyugo@gmail.com>
 * @license    http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version    Release: 1.0.0
 * @since      Class available since Release 1.2.0
 */
class StripeEngine
{
    public static function connect() 
    {
        // This should not be called if the user is existing with keys ID or Email
        App::vendor('stripe/stripe-php/init');

        //Config::get('ENVIRONMENT');
        $stripeKey = Config::get('STRIPESK');
        
        \Stripe\Stripe::setApiKey( $stripeKey );
    }

    public static function getCustomer( $customerID ) 
    {
        self::connect();

        try {

            $c = \Stripe\Customer::retrieve( $customerID );

            return $c;
        } catch (\Stripe\Error\Base $e) {
            return false;//$e->getMessage() . "stripe";
        } catch (Exception $e) {
            return false;//$e->getMessage() . "code";
        }
    }

    public static function getCustomerStripe()
    {
        $user      = User::info();
        $theID     = ($user->ParentUserID) ? $user->ParentUserID : $user->UserID;
        $checkUser = User::info(false, $theID);

        return self::getCustomer( $checkUser->StripeCustomerID );
    }

    public static function checkCustomer( $customerID ) 
    {

        self::connect();

        try {

            $c = \Stripe\Customer::retrieve( $customerID );

            return isset($c->id) ? $c->id : false;
        } catch (\Stripe\Error\Base $e) {
            return false;//$e->getMessage() . "stripe";
        } catch (Exception $e) {
            return false;//$e->getMessage() . "code";
        }
    }

    public static function getCharge( $chargeID ) 
    {

        self::connect();

        try {
            $charge = \Stripe\Charge::retrieve($chargeID);

            return $charge;
        } catch (\Stripe\Error\Base $e) {
            
            return $e->getMessage();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public static function getCharges( $limit = 10 ) 
    {

        self::connect();

        try {
            $charges = \Stripe\Charge::all([ 
                                            "limit" => $limit
                                        ]);

            $data = $charges->data; 
            $data = array_reverse($data); 
            
            return $data;
        } catch (\Stripe\Error\Base $e) {
            
            return $e->getMessage();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public static function getChargesByIntent( $intentID ) 
    {

        self::connect();

        try {
            
            $charges = \Stripe\Charge::all([
                'payment_intent' => $intentID,
                // Limit the number of objects to return (the default is 10)
                'limit' => 1,
            ]);

            return $charges->data[0];
        } catch (\Stripe\Error\Base $e) {
            
            return false;
            //return 'Stripe Error: '. $e->getMessage();
        } catch (Exception $e) {
            return false;
            //return 'System Error: '. $e->getMessage();
        }
    }

    public static function getEvent( $eventID ) 
    {

        self::connect();

        try {
            $event = \Stripe\Event::retrieve($eventID);

            return $event;
        } catch (\Stripe\Error\Base $e) {
            
            return $e->getMessage();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public static function getEvents( $type = 'charge.succeeded', $limit = 10 ) 
    {

        self::connect();

        try {
            $eventLists = \Stripe\Event::all([
                                            "type" => $type, 
                                            "limit" => $limit
                                        ]);

            $events     = $eventLists->data;
            $events     = array_reverse($events);

            return $events;
        } catch (\Stripe\Error\Base $e) {
            
            return $e->getMessage();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public static function addCustomer( $data = array() ) 
    {

        self::connect();

        try {

            $user = $data['user'];
            $meta = $data['meta'];

            $desc = "Signed: ". date('Y-m-d H:i:s');

            $stripeCustomer = \Stripe\Customer::create(array(
                "name"        => $meta['FirstName'] . ' ' . $meta['LastName'],
                "email"       => $user['Email'],
                "description" => $desc,
                "metadata"    => [
                                    'First Name' => $meta['FirstName'],
                                    'Last Name'  => $meta['LastName']
                                ]
            ));

            return $stripeCustomer->id;

        } catch (\Stripe\Error\Base $e) {
            
            return $e->getMessage();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public static function addSubscription( $UserStripeID, $StripeIntentID, $StripePlanID, $CouponID = false ) 
    {

        self::connect();

        try {

            $intent = \Stripe\PaymentIntent::retrieve($StripeIntentID);

            $payment_method = \Stripe\PaymentMethod::retrieve($intent->payment_method);
            $payment_method->attach(['customer' => $UserStripeID]);

            $subscrData = array(
                "customer" => $UserStripeID,
                "items" => array(
                    array(
                        "plan" => $StripePlanID,
                    ),
                ),
                'metadata' => ['coupon' => 'None'],
                'default_payment_method' => $intent->payment_method,
                'trial_period_days' => 30
            );

            if($CouponID && strlen($CouponID) > 0) {
                $subscrData['coupon'] = $CouponID;
            }

            $subscr = \Stripe\Subscription::create($subscrData);

            return $subscr->id;

        } catch (\Stripe\Error\Base $e) {
            
            return $e->getMessage();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public static function updateSubscription( $SubscriptionID, $CouponID = "" ) 
    {

        self::connect();

        try {
            if($CouponID) {
                \Stripe\Subscription::update( $SubscriptionID, ['coupon' => $CouponID] );
            } else {
                $subs = \Stripe\Subscription::retrieve($SubscriptionID);
                $subs->deleteDiscount();
            }

            return true;

        } catch (\Stripe\Error\Base $e) {
            
            return $e->getMessage();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
    
    public static function removeSubscription( $SubscriptionID ) 
    {

        self::connect();

        try {

            $subscription = \Stripe\Subscription::retrieve(
                $SubscriptionID
            );
            $subscription->delete();            

        } catch (\Stripe\Error\Base $e) {
            
            return $e->getMessage();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public static function updateSubscriptionPaymentMethod( $SubscriptionID, $PmID ) 
    {

        self::connect();

        try {

            $subscrData = \Stripe\Subscription::retrieve($SubscriptionID);
            $subscrData->default_payment_method = $PmID;
            $subscrData->save();            

        } catch (\Stripe\Error\Base $e) {
            
            return $e->getMessage();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public static function getSubscription( $SubscriptionID ) 
    {

        self::connect();

        try {
            $subs = \Stripe\Subscription::retrieve($SubscriptionID);
            
            return $subs;

        } catch (\Stripe\Error\Base $e) {
            
            return $e->getMessage();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public static function createSource( $UserStripeID, $StripeToken ) 
    {

        self::connect();

        try {
            $customer = \Stripe\Customer::retrieve( $UserStripeID );
            $customer->sources->create(array("source" => $StripeToken));

            return $StripeToken;

        } catch (\Stripe\Error\Base $e) {
            
            return $e->getMessage();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public static function addSource( $UserStripeID, $StripeToken ) 
    {

        self::connect();

        try {
            $customer = \Stripe\Customer::retrieve( $UserStripeID );
            $customer->source = $StripeToken;
            $customer->save();

            return $StripeToken;

        } catch (\Stripe\Error\Base $e) {
            
            return $e->getMessage();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public static function customerPaymentMethods($customerID) 
    {
        self::connect();

        try {
            $StripeToken = \Stripe\PaymentMethod::all([
                'customer' => $customerID,
                'type' => 'card',
            ]);

            return $StripeToken;

        } catch (\Stripe\Error\Base $e) {
            
            return $e->getMessage();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public static function attachCustomer( $customerID, $charge = array() ) 
    {

        self::connect();

        try {

           $payment_method = \Stripe\PaymentMethod::retrieve($charge->payment_method);
           $payment_method->attach(['customer' => $customerID]);

            \Stripe\Charge::update(
                $charge->id,
                ['customer' => $customerID]
            );

            // $charged = self::getCharge( $charge->id );
            return $charge->id;

        } catch (\Stripe\Error\Base $e) {
            
            return $e->getMessage();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public static function deleteCustomer( $customerID ) 
    {

        self::connect();

        try {

            $customer = \Stripe\Customer::retrieve( $customerID );
            $customer->delete();

            return true;

        } catch (\Stripe\Error\Base $e) {
            
            return $e->getMessage();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public static function doPaymentIntentExisting( $amount = 4900, $customer, $payment, $desc = 'TorreviejaTranslators.com - Add Profile' ) 
    {

        self::connect();

        try {
            
            $intent = \Stripe\PaymentIntent::create([
                'amount' => $amount,
                'currency' => 'eur',
                'customer' => $customer,
                'payment_method' => $payment,
                'payment_method_types' => ['card'],
                'off_session' => true,
                'confirm' => true,
                'description' => $desc
            ]);

            return $intent;

        } catch (\Stripe\Error\Base $e) {
            
            return false;
            //return 'Stripe Error: '. $e->getMessage();
        } catch (Exception $e) {
            return false;
            //return 'System Error: '. $e->getMessage();
        }
    }

    public static function doPaymentIntent( $amount = 4900, $currency = 'usd', $desc = '', $customer = false ) 
    {

        self::connect();

        try {
            $data = [
                'amount' => $amount,
                'currency' => $currency,
                'setup_future_usage' => 'off_session',
                'description' => $desc
            ];

            if($customer) {
                $data['customer'] = $customer;
            }

            $intent = \Stripe\PaymentIntent::create($data);

            return $intent;

        } catch (\Stripe\Error\Base $e) {
            
            return false;
            //return 'Stripe Error: '. $e->getMessage();
        } catch (Exception $e) {
            return false;
            //return 'System Error: '. $e->getMessage();
        }
    }

    public static function updatePaymentIntent( $id, $amount, $currency = 'usd', $desc = '' ) 
    {

        self::connect();

        try {
            
            \Stripe\PaymentIntent::update(
                $id,
                [
                    'amount' => $amount,
                    'currency' => $currency,
                    'description' => $desc
                ]
            );

        } catch (\Stripe\Error\Base $e) {
            
            return false;
            //return 'Stripe Error: '. $e->getMessage();
        } catch (Exception $e) {
            return false;
            //return 'System Error: '. $e->getMessage();
        }
    }

    public static function doAuthorizeCustomer( $token, $meta, $prc = false ) 
    {

        $price       = ($prc) ? $prc : 4900;
        $custID      = $meta['StripeCustomerID'];
        $totalAmount = isset($meta['ProfileCount']) ? $meta['ProfileCount'] * $price : $price;

        self::connect();

        try {
            
            $customer = \Stripe\Customer::retrieve( $custID );
            $customer->sources->create( array("source" => $token) );

            $charge = \Stripe\Charge::create([
                'amount' => $totalAmount,
                'currency' => 'eur',
                'description' => 'DrivingLicences.es Processing Fee',
                "customer" => $custID,
                'capture' => false,
            ]);

            return $charge->id;

        } catch (\Stripe\Error\Base $e) {
            
            return false;
            //return 'Stripe Error: '. $e->getMessage();
        } catch (Exception $e) {
            return false;
            //return 'System Error: '. $e->getMessage();
        }
    }

    public static function doChargeCustomer( $chargeID ) 
    {

        self::connect();

        try {
            
            $charge = \Stripe\Charge::retrieve( $chargeID );
            $charge->capture();

        } catch (\Stripe\Error\Base $e) {
            
            return false;
            //return 'Stripe Error: '. $e->getMessage();
        } catch (Exception $e) {
            return false;
            //return 'System Error: '. $e->getMessage();
        }
    }

    public static function doChargeAll( $chargeID ) 
    {

        self::connect();

        try {
            
            $charges = \Stripe\Charge::all([
                'payment_intent' => $chargeID,
                // Limit the number of objects to return (the default is 10)
                'limit' => 3,
            ]);

            return $charges->data[0];
        } catch (\Stripe\Error\Base $e) {
            
            return false;
            //return 'Stripe Error: '. $e->getMessage();
        } catch (Exception $e) {
            return false;
            //return 'System Error: '. $e->getMessage();
        }
    }

    public static function doRefundCharge( $chargeID, $amount = false ) 
    {

        self::connect();

        try {
            
            $refundData = [
                "charge" => $chargeID,
                "reason" => 'requested_by_customer'               
            ];

            if($amount) {
                $refundData['amount'] = $amount * 100;
            }
            
            $charges = \Stripe\Refund::create($refundData);

            return $charges;

        } catch (\Stripe\Error\Base $e) {
            
            //return false;
            return 'Stripe Error: '. $e->getMessage();
        } catch (Exception $e) {
            //return false;
            return 'System Error: '. $e->getMessage();
        }
    }

    public static function checkUserStripe( $user )
    {
        $metaModel = App::load()->model('usermeta', true);
        $data = array();
        
        if(!self::checkCustomer( $user->StripeCustomerID )) {
            $data['user']['Email']     = $user->Email;
            $data['meta']['FirstName'] = $user->FirstName;
            $data['meta']['LastName']  = $user->LastName;
            $StripeCustomerID          = self::addCustomer( $data );

            $metaModel->doUpdateStripeID( $user->UserMetaID, $StripeCustomerID );            

            if(class_exists('Auth')) {
                Auth::updateUserSession();
            }
        }
    }

    public static function getCoupons( $limit = 3, $idOnly = false ) 
    {
        self::connect();

        $stripeCoupon = false;
        try {
            $stripeCoupons = \Stripe\Coupon::all(['limit' => $limit]);
            $return = $stripeCoupons->data;
            if($idOnly) {
                $ids = [];
                foreach($stripeCoupons->data as $d) {
                    $ids[] = $d->id;
                }

                $return = $ids;
            }

            return $return;
        } catch (\Stripe\Error\Base $e) {
            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function getCoupon( $couponID = '' ) 
    {
        self::connect();

        $stripeCoupon = false;
        try {
            $stripeCoupon = \Stripe\Coupon::retrieve( $couponID );

            return $stripeCoupon;
        } catch (\Stripe\Error\Base $e) {
            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function addCoupon( $params = array() ) 
    {
        self::connect();

        try {
            $stripeCoupon = \Stripe\Coupon::create($params);
            $couponID     = $stripeCoupon->id;

            return $couponID;
        } catch (\Stripe\Error\Base $e) {
            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function updateCoupon( $couponID, $name ) 
    {
        self::connect();

        try {
            $stripeCoupon = \Stripe\Coupon::retrieve( $couponID );
            $stripeCoupon->name = strtoupper($name);
            $stripeCoupon->save();
        } catch (\Stripe\Error\Base $e) {
            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function deleteCoupon( $couponID ) 
    {
        self::connect();

        try {
            $stripeCoupon = \Stripe\Coupon::retrieve( $couponID );
            $stripeCoupon->delete();
        } catch (\Stripe\Error\Base $e) {
            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function getStripeSubscriptionInfo( $id = '' ) 
    {

        self::connect();

        $stripeCoupon = false;
        try {
            $stripeCoupon = \Stripe\Subscription::retrieve($id);
        } catch (Exception $e) {
            $stripeCoupon = false;
        }

        return ($stripeCoupon) ? $stripeCoupon->discount->coupon : false;
    }

    public static function updateStripeCoupon( $sid, $coupon ) 
    {

        self::connect();

        $stripeCoupon = false;
        try {
            $stripeCoupon = \Stripe\Subscription::update(
                $sid,
                [
                    'coupon' => $coupon,
                ]
            );
        } catch (Exception $e) {
            $stripeCoupon = $e->getMessage();

        }

        return $stripeCoupon;
    }

    public static function checkStripeCoupon( $id = '' ) 
    {
        $c = getStripeCoupon( $id );
        return ($c) ? true : false;
    }

    public static function addNewProduct( $data = array() ) 
    {
        self::connect();
        try {
            $product = \Stripe\Product::create([
                "name" => $data['ProductName'],
                "type" => 'service',
                "active" => ($data['ProductActive'] == 1) ? true : false,
                "metadata" => ['tracks' => $data['ProductTrack'],'fulldescription' => $data['ProductDescription']]
            ]);
            
            return $product->id;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function updateProduct( $productID, $data = array() ) 
    {
        self::connect();
        try {
            $product = \Stripe\Product::update($productID, [
                "name" => $data['ProductName'],
                "active" => ($data['ProductActive'] == 1) ? true : false,
                "metadata" => ['tracks' => $data['ProductTrack'],'fulldescription' => $data['ProductDescription']]
            ]);
            
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function deactivateProduct( $productID ) 
    {
        self::connect();
        try {
            $product = \Stripe\Product::update($productID, [
                "active" => false
            ]);
            
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function deleteProduct( $productID ) 
    {
        self::connect();
        try {

            $plans = (array) self::getAllProductPlans( $productID );

            if(count($plans)) {
                foreach($plans as $plan) {
                    $plan = \Stripe\Plan::retrieve( $plan->id );
                    $plan->delete();
                }
            }

            $product = \Stripe\Product::retrieve( $productID );
            $product->delete();
            
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function getAllProductPlans( $productID, $idOnly = false ) 
    {
        self::connect();
        try {
            $plans = \Stripe\Plan::all(['product' => $productID, 'limit' => 100]);
            
            $return = $plans->data;

            if($idOnly) {
                $ids = [];
                foreach($plans->data as $d) {
                    $ids[] = $d->id;
                }

                $return = $ids;
            }

            return $return;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function addNewProductPlan( $data = array() ) 
    {
        self::connect();

        try {
            $plan = \Stripe\Plan::create([
                "nickname" => $data['PlanName'],
                "active"   => ($data['PlanActive'] == 1) ? true : false,
                "amount"   => $data['PlanAmount'],
                "interval" => $data['PlanInterval'],
                "interval_count" => $data['PlanIntervalCount'],
                "product"  => $data['StripeProductID'],
                "currency" => strtolower($data['PlanCurrency'])
            ]);
            return $plan->id;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function updateProductPlan( $planID, $data = array() ) 
    {
        self::connect();
        try {
            $plan = \Stripe\Plan::update( $planID, [
                "nickname" => $data['PlanName'],
                "active"   => ($data['PlanActive'] == 1) ? true : false
            ]);
            
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function deleteProductPlan( $planID ) 
    {
        self::connect();
        try {

            $product = \Stripe\Plan::retrieve( $planID );
            $product->delete();
            
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function deactivateProductPlan( $planID ) 
    {
        self::connect();
        try {

            $plan = \Stripe\Plan::update( $planID, [
                "active"   => false
            ]);
            
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
?>