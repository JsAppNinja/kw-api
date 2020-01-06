<?php
/**
 * api.kw.dev
 * @SWG\Swagger(
 *     schemes={"http"},
 *     host="localhost:8000",
 *     basePath="/v1",
 *     @SWG\Info(
 *         version="1.0.0",
 *         title="Swagger KW-API",
 *         description="This is KW-API messaging middleware for dispatch data to services / applications",
 *         @SWG\Contact(
 *             email="2light.hidayah@gmail.com"
 *         ),
 *     ),
 *     @SWG\ExternalDocumentation(
 *         description="Find out more about Swagger",
 *         url="http://swagger.io"
 *     )
 * )
 * @SWG\SecurityScheme(
 *   securityDefinition="apiKey",
 *   type="apiKey",
 *   in="header",
 *   name="apiKey",
 * )
 */

namespace App\Http\Controllers;


use App\Http\Requests\CommunicationRequest;
use Twilio;

class CommunicationsController extends Controller
{

    /**
     * @SWG\Post(
     *     path="/communications/send_text",
     *     tags={"communications"},
     *     operationId="send sms",
     *     summary="Send sms",
     *     description="",
     *     consumes={"application/json", "application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         name="phoneNumber",
     *         in="formData",
     *         description="Destination phone number",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         name="message",
     *         in="formData",
     *         description="short message",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Response(
     *         response=405,
     *         description="Invalid input",
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="Bad Request",
     *     ),
     *     security={
     *       {"apiKey": {}},
     *     }
     * )
     */
    public function sendText(CommunicationRequest $request)
    {
        $data = $request->only('phoneNumber', 'message');

        try {
            
            $twilio = Twilio::message($data['phoneNumber'], $data['message']);
            
            // @TODO Log Response
            // $response = [
            //     'date_created' => $twilio->date_created,
            //     'date_updated' => $twilio->date_updated,
            //     'sid' => $twilio->sid,
            //     'account_sid' => $twilio->account_sid,
            //     'body' => $twilio->body,
            //     'num_segments' => $twilio->num_segments,
            //     'num_media' => $twilio->num_media,
            //     'subresource_uris' => $twilio->subresource_uris,
            //     'from' => $twilio->from,
            //     'to' => $twilio->to
            // ];

            $response = [
                'from' => $twilio->from,
                'to' => $twilio->to,
                'body' => $twilio->body,
            ];

        } catch (\Services_Twilio_RestException $e) {
            // @todo Log Error
            throw $e;
        }

        return response()->json($response);
    }
}
