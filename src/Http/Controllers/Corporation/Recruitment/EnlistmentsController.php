<?php


namespace Seatplus\Web\Http\Controllers\Corporation\Recruitment;


use Seatplus\Eveapi\Models\Recruitment\Enlistments;
use Seatplus\Web\Http\Controllers\Controller;
use Seatplus\Web\Http\Controllers\Request\CreateOpenRecruitmentRequest;

class EnlistmentsController extends Controller
{

    public function index()
    {
        return Enlistments::with('corporation', 'corporation.alliance')->get()->toJson();
    }

    public function create(CreateOpenRecruitmentRequest $request)
    {

        $enlistment = Enlistments::updateOrCreate(
            ['corporation_id' => $request->get('corporation_id')],
            ['type' => $request->get('type')]
        );

        return redirect()->back()->with('success', 'enlistment created');
    }

    public function delete(int $corporation_id)
    {
        Enlistments::where('corporation_id',$corporation_id)->delete();

        return back()->with('success', 'corporation is closed for recruitment');
    }

}
