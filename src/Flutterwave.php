<?php

namespace jaysparmar\flutterwave;

use DateTime;

/**
 * Custom Flutterwave Library
 *
 *  This is the lightweight Custom flutterwave library which helps you to use flutterwave api where all the functions has the
 *  standard return format of :
 *
 *  [
 *      "error" = true / false,
 *      "message" = "The Message recieved from Flutterwave",
 *      "data" = "The Message recieved from Flutterwave"
 *  ]
 *
 * PHP version > 7.3.x
 *
 * Provided Methods :
 *  1.  balances($currency = "")
 *  2.  get_all_transactions($config)
 *  3.  create_virtual_account($config)
 *  4.  get_virtual_account($reference)($reference)
 *  5.  transfer_rate($amount, $source, $destination)
 *  6.  get_available_bills($type = "all")
 *  7.  validate_bill($item_code, $code, $customer)
 *  8.  pay_bill($country, $customer, $amount, $type, $reference)
 *  9.  get_bill_status(int $reference)
 *  10. get_bill_payments(string $from, string  $to, string  $page = "", string  $customer_id = "")
 *
 * LICENSE: This source file is subject to version 8.1.1 of the PHP license
 * that is available through the world-wide-web. If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to bharticloud@gmail.com, so we can mail you a copy immediately.
 *
 * @category   Flutterwave custom Library
 * @package    jaysparmar/flutterwave-sdk
 * @author     Original Author <bharticloud@gmail.com>
 * @version    1.0
 * @since      2025
 *
 *
 */
class Flutterwave
{

    protected string $url;
    protected string $public_key;
    protected string $secret_key;
    protected string $encryption_key;
    protected string $currency_code;
    protected string $card_webhook_url;

    protected string $admin_debit_currency = "NGN";
    protected array $supported_currencies = ["NGN", "KES", "GHS", "USD", "EUR", "ZAR", "GBP", "TZS", "UGX", "RWF", "ZMW", "INR", "XOF", "MUR", "ETB", "JPY", "MAD", "XAF", "AUD", "CAD", "MYR", "CNY", "BRL", 'eNGN', "MWK"];
    public array $available_bills = ["airtime" => "Airtime", "data_bundle" => "Data Bundle", "power" => "Power", "internet" => "Internet", "toll" => "Toll", "biller_code" => "biller_code", "cables" => "cable", "all" => "all"];

    public function __construct($public_key, $secret_key, $encryption_key, $currency_code, $card_webhook_url, $url = 'https://api.flutterwave.com/v3/')
    {
        $this->url = $url;
        $this->public_key = $public_key;
        $this->secret_key = $secret_key;
        $this->encryption_key = $encryption_key;
        $this->currency_code = $currency_code;
        $this->card_webhook_url = $card_webhook_url;
    }


    /**
     * @param string $currency
     * Leave it empty to get all the balances from supported currencies and pass the currency code to get the balance for the currency.
     * @return array
     * [
     *  "error" => bool,
     *  "message" => string,
     *  "data" => array
     * ]
     */
    public function balances(string $currency = "") : array
    {
        $response = $this->curl($this->api_url('balances'));
        if ($response['code'] == 200) {
            if ($currency == "") {
                return $this->send(json_decode($response['data'], true)['data'], json_decode($response['data'], true)['message'], false);
            }

            if (!in_array($currency, $this->supported_currencies)) {
                return $this->send([], "Currency not supported..");
            }

            $data = json_decode($response['data'], true);
            $data = $data['data'];
            for ($i = 0; $i < count($data); $i++) {
                if ($data[$i]['currency'] == $currency) {
                    $message = json_decode($response['data'], true)['message'];
                    $response['data'] = json_encode($data[$i]);
                    return $this->send(json_decode($response['data'], true), $message, false);
                }
            }
        }
            return $this->send([], "Something went wrong..");

    }



    /**
     * @param array $config
     *  All Possible keys :-
     *      [
     *       "from" => "2020-01-01",
     *       "to" => "2020-01-14",
     *       "page" => 1,
     *       "customer_email" => "bharticloud@gmail.com",
     *       "status" => 'successful',
     *       "tx_ref" => "adasdahwv",
     *       "customer_fullname" => "jay parmar",
     *       "currency" => "NGN"
     *      ]
     * @return array
     * [
     *  "error" => bool,
     *  "message" => string,
     *  "data" => array
     * ]
     */
    public function get_all_transactions(array $config): array
    {
        $str = "";
        if (isset($config['from']) && isset($config['to'])) {
            $str .= "from=" . $config['from'] . "&to=" . $config['to'] . "&";
        }
        if (isset($config['page'])) {
            $str .= "page=" . $config['page'] . "&";
        }
        if (isset($config['customer_email'])) {
            $str .= "customer_email=" . $config['customer_email'] . "&";
        }
        if (isset($config['status'])) {
            $str .= "status=" . $config['status'] . "&";
        }
        if (isset($config['tx_ref'])) {
            $str .= "tx_ref=" . $config['tx_ref'] . "&";
        }
        if (isset($config['customer_fullname'])) {
            $str .= "customer_fullname=" . $config['customer_fullname'] . "&";
        }
        if (isset($config['currency'])) {
            $str .= "currency=" . $config['currency'] . "&";
        }
        if ($str == "") {
            return $this->send([], "Please check the keys in the config.");
        }
        $response = $this->curl($this->url . "transactions?$str" );
        if ($response['code'] == 200) {
            return $this->send(json_decode($response['data'], true), "Transactions recieved Successfully.");
        } else {
            return $this->send([], "Something went wrong..");
        }
    }

    /**
     * @param array $config
     *  All Possible keys :-
     *      [
     *       "email" => "bharticloud@gmail.com",
     *       "is_permanent" => true,
     *       "bvn" => "12345678901",
     *       "phonenumber" => "08109328188",
     *       "firstname" => "Angela",
     *       "lastname" => "Ashley",
     *       "narration" => "Angela Ashley-Osuzoka"
     *      ]
     * @return array
     * [
     *  "error" => bool,
     *  "message" => string,
     *  "data" => array
     * ]
     */
    public function create_virtual_account(array $config): array
    {
        // array_push("callback_url", $this->card_webhook_url);

        $response =  $this->curl($this->url . "virtual-account-numbers", "POST", $config, true);
        $body = $response['data'];
        if ($response['code'] == 200) {
            if ($body['status'] = "success") {
                return $this->send($body["data"], $body["message"], false);
            }
        }
        return $this->send([], $body['message']);
    }


    /**
     * @param string $reference
     * @return array
     * [
     *  "error" => bool,
     *  "message" => string,
     *  "data" => array
     * ]
     */
    public function get_virtual_account(string $reference): array
    {
        if ($reference == "") {
            return $this->send([], "Reference cannot be empty..");
        }
        $response =  $this->curl($this->url . "virtual-account-numbers/" . $reference);
        if ($response['code'] == 200) {
            $response = json_decode($response['data'], true);
            return $this->send($response['data'], $response['message'], false);
        } else {
            return $this->send([], "Something went wrong");
        }
    }


    /**
     * @param mixed $amount Amount that you want to check transfer rates.
     * @param string $source source currency - Should be in the range given below.
     * @param string $destination destination currency -  Should be in the range given below.
     * range :- ["NGN", "KES", "GHS", "USD", "EUR", "ZAR", "GBP", "TZS", "UGX", "RWF", "ZMW", "INR", "XOF", "MUR", "ETB", "JPY", "MAD", "XAF", "AUD", "CAD", "MYR", "CNY", "BRL", 'eNGN', "MWK"]
     * @return array
     * [
     *  "error" => bool,
     *  "message" => string,
     *  "data" => array
     * ]
     */
    public function transfer_rate(float $amount, string $source, string $destination): array
    {
        $source_flag = $dest_flag = false;
        $msg = "";
        if (!in_array($source, $this->supported_currencies)) {
            $source_flag = true;
            $msg .= "Source currency ";
        }
        if (!in_array($destination, $this->supported_currencies)) {
            $dest_flag = true;
            $msg .= $source_flag ? "and Destination currency " : "Destination currency ";
        }
        if ($source_flag || $dest_flag) {
            $msg .= "not supported.";
            return $this->send([], $msg);
        }
        $response = $this->curl($this->api_url("transfers/rates?amount=$amount&destination_currency=$destination&source_currency=$source"));
        if ($response['code'] == 200) {
            $response = json_decode($response['data'], true);
            return $this->send($response['data'], $response['message'], false);
        } else {
            return $this->send([], "Something went wrong");
        }
    }


    public function create_transfer($bank_code, $account_number, $amount, $txn_ref, array $meta): array
    {
        $url = $this->api_url("transfers");
        $data = [
            "account_bank" =>  $bank_code,
            "account_number" => $account_number,
            "amount" => $amount,
            "narration" => "Kudiwave  transfer",
            "currency" => "NGN",
            "reference" => $txn_ref,
            "callback_url" => $this->card_webhook_url,
            "debit_currency" => $this->admin_debit_currency,
            "meta" => $meta
        ];
        $response = $this->curl($url, "POST", $data, true);
        if ($response['code'] == 200) {
            $response = $response['data'];
            return $this->send($response['data'], $response['message'], false);
        } else {
            return $this->send([], "Something went wrong");
        }
    }


    /**
     *
     * Get all the bill payment options.
     *
     *
     * @param mixed $type bill type.
     *
     * range :- ["airtime", "data_bundle", "power", "internet", "toll", "biller_code", "cables", "all"]
     *
     * @return array
     *  [
     *      "error" => bool,
     *      "message" => string,
     *      "data" => array
     *  ]
     *
     * Flow :
     *  + get_bills($type), Get all the available bills
     *  + validate_bill($item_code, $code, $customer), Validate Bills
     *  + pay_bill($country, $customer, $amount, $type, $reference), Pay the bill.
     *  + get_bill_status(int $reference) , check payment status.
     */
    public function get_available_bills(string $type = "all", $biller_code = "", $country = "NG"): array
    {
        if (!array_key_exists($type, $this->available_bills)) {
            return $this->send(["available_bills" => $this->available_bills], "Please enter correct Bill type");
        }
        $type_url = "https://api.flutterwave.com/v3/bill-categories?$type=1";
        $biller_url = "https://api.flutterwave.com/v3/bill-categories?biller_code=$biller_code";
        $all = "https://api.flutterwave.com/v3/bill-categories?airtime=1&data_bundle=1&power=1&internet=1&toll=1&biller_code=1&cables=1";
        if ($type === "all") {
            $response = $this->curl($all, "GET", [], true);
        } elseif ($type === "biller_code") {
            $response = $this->curl($biller_url, "GET", [], true);
        } else {
            $response = $this->curl($type_url, "GET", [], true);
        }
        // print_r($response);
        // die();
        $body = $response['data'];
        if ($response['code'] == 200) {
            if ($body['status'] = "success") {
                $arr = array();
                if (!empty($body['data'])) {
                    foreach ($body['data'] as $row) {
                        //round commission
                        $row['default_commission'] = round($row['default_commission']);


                        if ($row['country'] == $country) {
                            $arr[] = $row;
                        }
                    }
                }
                return $this->send($arr, $body['message'], false);
            }
        }
        return $this->send([], $body['message']);
    }



    /**
     *
     * This function validates the bill payments recieved from get_bills($type) function
     *
     *
     * @param string $item_code This is the item_code returned from get_bills($type) -> data
     * @param string $code This is the biller code for the service.
     * @param string $customer This is the customer identifier For airtime, the value must be the customer's phone number. For DSTV, it must be the customer's smartcard number.
     *
     *
     * @return array
     *  [
     *      "error" => bool,
     *      "message" => string,
     *      "data" => array
     *  ]
     *
     * Flow :
     *  + get_bills($type), Get all the available bills
     *  + validate_bill($item_code, $code, $customer), Validate Bills
     *  + pay_bill($country, $customer, $amount, $type, $reference), Pay the bill.
     *  + get_bill_status(int $reference) , check payment status.
     *
     **/
    public function validate_bill(string $item_code, string $code, string $customer): array
    {
        $url = "https://api.flutterwave.com/v3/bill-items/$item_code/validate?code=$code&customer=$customer";
        $response = $this->curl($url, "GET", [], true);

        $body = $response['data'];
        if ($response['code'] == 200) {
            if ($body['status'] = "success") {
                return $this->send($body['data'], $body['message'], false);
            }
            return $this->send([], $body['message']);
        }
        return $this->send([], "Item could not be validated");

    }



    /**
     *
     *  After validating the bill using validate_bill($item_code, $code, $customer) function
     *  you can pay the bill from this function.
     *
     *
     * @param string $country Country name.
     * @param string $customer Phone no., smartcard no., etc.
     * @param string $amount amount of bill.
     * @param string $type Fetch the possible values to pass from get_bills() -> data -> biller_name .
     * @param string $reference unique transaction id sent by developer.
     *
     *
     * @return array
     *  [
     *      "error" => bool,
     *      "message" => string,
     *      "data" => array
     *  ]
     *
     * Flow :
     *  + get_bills($type), Get all the available bills
     *  + validate_bill($item_code, $code, $customer), Validate Bills
     *  + pay_bill($country, $customer, $amount, $type, $reference), Pay the bill.
     *  + get_bill_status(int $reference) , check payment status.
     *
     **/
    public function pay_bill(string $country, string $customer, string $type, string $reference, string $amount): array
    {
        $requestBody = array(
            'country' => $country,
            'customer' => $customer,
            'amount' => $amount,
            'type' => $type,
            "reference" => $reference
        );

        $response = $this->curl("https://api.flutterwave.com/v3/bills", "POST", $requestBody, true);
        $body = $response['data'];
        if ($response['code'] == 200) {
            if ($body['status'] = "success") {
                return $this->send($body['data'], $body['message'], false);
            }
            return $this->send([], $body['message']);
        } else {
            return $this->send([], json_encode($response));
        }
    }

    /**
     *
     * Get all the bill payment status.
     *
     *
     * @param int $reference , the reference which you have sent in pay_bill($country, $customer, $amount, $type, $reference)

     *
     *
     * @return array
     *  [
     *      "error" => bool,
     *      "message" => string,
     *      "data" => array
     *  ]
     *
     * Flow :
     *  + get_bills($type), Get all the available bills
     *  + validate_bill($item_code, $code, $customer), Validate Bills
     *  + pay_bill($country, $customer, $amount, $type, $reference), Pay the bill.
     *  + get_bill_status(int $reference) , check payment status.
     *
     **/
    public function get_bill_status(int $reference): array
    {
        $url = "https://api.flutterwave.com/v3/bills/$reference";
        $response = $this->curl($url, "GET", [], true);
        $body = $response['data'];
        if ($response['code'] == 200) {
            if ($body['status'] = "success") {
                return $this->send($body['data'], $body['message'], false);
            }
        }
        return $this->send([], $body['message']);
    }


    /**
     *
     * This function fetches all the bills from perticular date.
     *
     *
     * @param string $from , From date in YYYY-MM-DD format
     * @param string $to , to date in YYYY-MM-DD format
     * @param string $page , Page reference
     * @param string $customer_id  could be anything like phone no. or smart card no., etc.
     *
     *
     * @return array
     *  [
     *      "error" => bool,
     *      "message" => string,
     *      "data" => array
     *  ]
     *
     **/
    public function get_bill_payments(string $from, string  $to, string  $page = "", string  $customer_id = ""): array
    {
        if (!(validateDate($from) && validateDate($from))) {
            return $this->send([], "Please enter valid Date.");
        }
        $url = "https://api.flutterwave.com/v3/bills?from=$from&to=$to";
        if ($page != "") {
            $url .= "&page=20";
        }
        if ($customer_id != "") {
            $url .= "&reference=%2B233494850059";
        }
        $response = $this->curl($url, "GET", [], true);
        $body = $response['data'];
        if ($response['code'] == 200) {
            if ($body['status'] = "success") {
                return $this->send($body['data'], $body['message'], false);
            }
        }
        return $this->send([], $body['message']);
    }


    // bvn

    public function verify_bvn(string $bvn_number): array
    {
        $url = $this->api_url("kyc/bvns/$bvn_number");
        $response = $this->curl($url, "GET", [], true);
        $body = $response['data'];
        if ($response['code'] == 200) {
            if ($body['status'] = "success") {
                return $this->send($body['data'], $body['message'], false);
            }
        }
        return $this->send([], $body['message']);
    }


    // Cards api functions

    /**
     *
     * This function Creates the new virtual Prepaid card.
     *
     *
     * @param $unique_ref
     * @param string $currency , Card Currency
     * @param int $amount , amount should be greater that flutterwave charges to get the balance in card.
     * @param string $billing_name , Name of the card Owner
     * @param string $billing_address , Billing address of the card Owner.
     * @param string $billing_city
     * @param string $billing_state
     * @param string $billing_postal_code
     * @return array
     *  [
     *      "error" => bool,
     *      "message" => string,
     *      "data" => array
     *  ]
     */
    public function create_virtual_card($unique_ref, string $currency, int $amount, string $billing_name, string $billing_address = "", string $billing_city = "", string $billing_state = "", string $billing_postal_code = ""): array
    {
        $requestBody = array(
            'currency' => $currency,
            'amount' => $amount,
            'debit_currency' => $this->admin_debit_currency,
            'billing_name' => $billing_name
        );

        if ($billing_address != "") {
            $requestBody['billing_address'] = $billing_address;
        }
        if ($billing_city != "") {
            $requestBody['billing_city'] = $billing_city;
        }
        if ($billing_address != "") {
            $requestBody['billing_address'] = $billing_address;
        }
        if ($billing_postal_code != "") {
            $requestBody['billing_postal_code'] = $billing_postal_code;
        }
        if ($billing_state != "") {
            $requestBody['billing_state'] = $billing_state;
        }

        $requestBody['callback_url'] = $this->card_webhook_url . "/card/$unique_ref";
        $response = $this->curl($this->api_url('virtual-cards'), "POST", $requestBody, true);
        $body = $response['data'];
        if ($response['code'] == 200) {
            if ($body['status'] = "success") {
                return $this->send($body['data'], $body['message'], false);
            }
        }
        return $this->send([], $body['message']);
    }


    public function get_all_cards(int $page = 1): array
    {
        $response = $this->curl($this->api_url("virtual-cards?page=$page"), "GET", [], true);
        $body = $response['data'];
        if ($response['code'] == 200) {
            if ($body['status'] = "success") {
                return $this->send($body['data'], $body['message'], false);
            }
        }
        return $this->send([], $body['message']);
    }


    public function get_card(string $card_id): array
    {
        $url = $this->api_url("virtual-cards/$card_id");
        $response = $this->curl($url, "GET", [], true);
        $body = $response['data'];
        if ($response['code'] == 200) {
            if ($body['status'] = "success") {
                return $this->send($body['data'], $body['message'], false);
            }
        }
        return $this->send([], $body['message']);
    }

    public function fund_card(int $amount, string $card_id, string $debit_currency = ""): array
    {
        $data = array(
            "amount" => $amount,
            "debit_currency" => $debit_currency
        );
        if (trim($data['debit_currency']) == "") {
            $data['debit_currency'] = $this->admin_debit_currency;
        }
        $url = $this->api_url("virtual-cards/$card_id/fund");
        $response = $this->curl($url, "POST", $data, true);
        $body = $response['data'];
        if ($response['code'] == 200) {
            if ($body['status'] = "success") {
                return $this->send($body['data'], $body['message'], false);
            }
        }
        return $this->send([], $body['message']);
    }


    public function terminate_card(string $card_id): array
    {
        $url = $this->api_url("virtual-cards/$card_id/terminate");
        $response = $this->curl($url, "PUT", [], true);
        $body = $response['data'];
        if ($response['code'] == 200) {
            if ($body['status'] = "success") {
                return $this->send($body['data'], $body['message'], false);
            }
        }
        return $this->send([], $body['message']);
    }

    public function get_card_transactions(string $card_id, string $from, string $to, int $per_page_transaction, int $page_no): array
    {
        if (!(validateDate($from) && validateDate($from))) {
            return $this->send([], "Please enter valid Date.");
        }
        $url = $this->api_url("virtual-cards/$card_id/transactions?from=$from&to=$to&index=$page_no&size=$per_page_transaction");
        $response = $this->curl($url, "GET", [], true);
        $body = $response['data'];
        if ($response['code'] == 200) {
            if ($body['status'] = "success") {
                return $this->send($body['data'], $body['message'], false);
            }
        }
        return $this->send([], $body['message']);
    }

    public function withdraw_from_card(string $amount, string $card_id): array
    {
        $data = [
            "amount" => $amount
        ];
        $url = $this->api_url("virtual-cards/$card_id/withdraw");
        $response = $this->curl($url, "POST", $data, true);
        $body = $response['data'];
        if ($response['code'] == 200) {
            if ($body['status'] = "success") {
                return $this->send($body['data'], $body['message'], false);
            }
        }
        return $this->send([], $body['message']);
    }

    public function transfer_fees(string $amount, String $currency = "NGN"): array
    {

        $url = $this->api_url("/transfers/fee?amount=$amount&currency=$currency");
        $response = $this->curl($url, "GET", [], true);
        $body = $response['data'];
        if ($response['code'] == 200) {
            if ($body['status'] = "success") {
                return $this->send($body['data'], $body['message'], false);
            }
        }
        return $this->send([], $body['message']);
    }


    public function block_card(string $card_id, string $status = "block"): array
    {
        if (!in_array($status, ["block", "unblock"])) {
            return $this->send([], "Please Provide valid card status.");
        }

        $status = trim($status);
        $card_id = trim($card_id);
        $url = $this->api_url("virtual-cards/$card_id/status/$status");
        $response = $this->curl($url, "PUT", [], true);
        $body = $response['data'];
        if ($response['code'] == 200) {
            if ($body['status'] = "success") {
                return $this->send($body['data'], $body['message'], false);
            }
        }
        return $this->send([], $body['message']);
    }

    public function resolve_account_number(string $account_number, string $bank_code = "block"): array
    {
        $url = $this->api_url("accounts/resolve");
        $response = $this->curl($url, "POST", [
            "account_number" => $account_number,
            "account_bank" => $bank_code
        ], true);
        $body = $response['data'];
        if ($response['code'] == 200) {
            if (!empty($body["data"])) {
                if ($body['status'] == "success") {
                    $data = [];
                    if (isset($body['data'])) {
                        $data = $body['data'];
                    }
                    return $this->send($data, $body['message'], false);
                }
            }
        }
        if (isset($body["message"]) && $body['message'] != "") {
            return $this->send([], $body['message']);
        }
        return $this->send([], "No account found");
    }


    public function send_otp(string $name, string $email, int $phone, array $medium = ["email", "whatsapp", "sms"]): array
    {
        $data = (object) array(
            "length" => 6,
            "customer" => (object)array(
                "name" => $name,
                "email" => $email,
                "phone" => $phone
            ),
            "sender" => "Kudiwave",
            "send" => "true",
            "medium" => $medium,
            "expiry" => 5
        );
        $url = $this->api_url("otps");
        $response = $this->curl($url, "POST", $data, true);

        $body = $response['data'];
        if ($response['code'] == 200) {
            if ($body['status'] = "success") {
                return $this->send($body['data'], $body['message'], false);
            }
        }
        return $this->send([], $body['message']);
    }

    public function verify_otp($otp, $reference): array
    {
        $url = $this->api_url("otps/$reference/validate");
        $data = [
            "otp" => $otp
        ];
        $response = $this->curl($url, "POST", $data, true);
        $body = $response['data'];
        if ($response['code'] == 200) {
            if ($body['status'] = "success") {
                return $this->send([], $body['message'], false);
            }
        }
        return $this->send([], $body['message']);
    }

    public function get_all_banks($country_code = "NG"): array
    {
        $url = (!empty($country_code)) ? "banks/$country_code" : "banks/NG";
        $url = $this->api_url($url);
        $response = $this->curl($url, "GET", [], true);
        $body = $response['data'];
        if ($response['code'] == 200) {
            if ($body['status'] = "success") {
                return $this->send($body['data'], $body['message'], false);
            }
        }
        return $this->send([], $body['message']);
    }

    public function get_bank_branches($bank_id): array
    {
        $url = $this->api_url("banks/$bank_id/branches");
        $response = $this->curl($url, "GET", [], true);
        $body = $response['data'];
        if ($response['code'] == 200) {
            if ($body['status'] = "success") {
                return $this->send($body['data'], $body['message'], false);
            }
        }
        return $this->send([], $body['message']);
    }

    // General functions
    public function send($data, string $message = "", bool $error = true): array
    {
        return [
            "error" => $error,
            "message" => $message,
            "data" => $data
        ];
    }

    public function curl(string $end_point, string $method = "GET",  $data = array(), bool $decode_dody = false): array
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $end_point,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => strtoupper($method),
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json",
                "Authorization: Bearer " . $this->secret_key
            ),
        ));
        $response = curl_exec($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        return [
            'data' => ($decode_dody) ? json_decode($response, true) : ($response),
            'code' => $httpcode
        ];
    }
    public function api_url(string $uri): string
    {
        return $this->url . $uri;
    }
}


// Library heplers :-

function validateDate($date, $format = 'Y-m-d'): bool
{
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
}