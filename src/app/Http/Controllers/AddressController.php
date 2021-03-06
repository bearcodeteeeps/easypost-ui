<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \EasyPost\EasyPost;
use \EasyPost\Address;

EasyPost::setApiKey(env('EASYPOST_API_KEY'));

class AddressController extends Controller
{
    /**
     * createAddress
     *
     * @param Request $request
     * @return void
     */
    public function createAddress(Request $request)
    {
        request()->validate([
            'street1'   => 'required|string',
            'street2'   => 'nullable|string',
            'city'      => 'required|string',
            'state'     => 'required|string',
            'zip'       => 'required|string',
            'country'   => 'nullable|string',
            'company'   => 'nullable|string',
            'phone'     => 'nullable',
        ]);

        try {
            $address = Address::create(
                array(
                    "verify"  => array("delivery"),
                    "street1" => request()->get('street1'),
                    "street2" => request()->get('street2'),
                    "city"    => request()->get('city'),
                    "state"   => request()->get('state'),
                    "zip"     => request()->get('zip'),
                    "country" => request()->get('country'),
                    "company" => request()->get('company'),
                    "phone"   => request()->get('phone'),
                )
            );
        } catch (Error $exception) {
            return back()->withError($exception->getMessage())->withInput();
        }

        $response = $address;

        if ($address->verifications->delivery->success == false) {
            session()->flash("error", "ADDRESS ENTERED IS NOT A VERIFIED ADDRESS BUT THE RECORD WAS CREATED ANYWAY.");
            return redirect()->back()->with(['response' => $response]);
        }

        session()->flash("message", "ADDRESS CREATED");
        return redirect()->back()->with(['response' => $response]);
    }

    /**
     * retrieveAddress
     *
     * @param Request $request
     * @return void
     */
    public function retrieveAddress(Request $request)
    {
        try {
            $address = Address::retrieve(request()->get('id'));
        } catch (Error $exception) {
            return back()->withError($exception->getMessage())->withInput();
        }

        $response = $address;

        session()->flash("message", "ADDRESS RETRIEVED");
        return redirect()->back()->with(['response' => $response]);
    }

    /**
     * retrieveAddresses
     *
     * @param Request $request
     * @return void
     */
    public function retrieveAddresses(Request $request)
    {
        try {
            $addresses = Address::all(array(
                # "page_size" => 2,
                "start_datetime" => "2016-01-02T08:50:00Z"
              ));
        } catch (Error $exception) {
            return back()->withError($exception->getMessage())->withInput();
        }

        $response = $addresses;

        session()->flash("message", "ADDRESSES RETRIEVED");
        return redirect()->back()->with(['response' => $response]);
    }
}
