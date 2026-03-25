<?php
namespace App\Http\Controllers;

use App\Models\SoftwareVersion;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;

class SoftwareController extends Controller
{
    public function index()
    {
        return Inertia::render('Software/Download');
    }

    public function checkSoftware(Request $request)
    {
        $version = $request->input('version');
        $mcuVersion = $request->input('mcuVersion');
        $hwVersion = $request->input('hwVersion');

        if (empty($version)) {
            return response()->json(['msg' => 'Version is required'], 200);
        }

        if (empty($hwVersion)) {
            return response()->json(['msg' => 'HW Version is required'], 200);
        }

        $patternST = '/^CPAA_[0-9]{4}\.[0-9]{2}\.[0-9]{2}(_[A-Z]+)?$/i';
        $patternGD = '/^CPAA_G_[0-9]{4}\.[0-9]{2}\.[0-9]{2}(_[A-Z]+)?$/i';
        $patternLCI_CIC = '/^B_C_[0-9]{4}\.[0-9]{2}\.[0-9]{2}$/i';
        $patternLCI_NBT = '/^B_N_G_[0-9]{4}\.[0-9]{2}\.[0-9]{2}$/i';
        $patternLCI_EVO = '/^B_E_G_[0-9]{4}\.[0-9]{2}\.[0-9]{2}$/i';

        $hwVersionBool = false;
        $stBool = false;
        $gdBool = false;
        $isLCI = false;
        $lciHwType = '';

        if (preg_match($patternST, $hwVersion)) {
            $hwVersionBool = true;
            $stBool = true;
        }
        if (preg_match($patternGD, $hwVersion)) {
            $hwVersionBool = true;
            $gdBool = true;
        }
        if (preg_match($patternLCI_CIC, $hwVersion)) {
            $hwVersionBool = true;
            $isLCI = true;
            $lciHwType = 'CIC';
            $stBool = true;
        } elseif (preg_match($patternLCI_NBT, $hwVersion)) {
            $hwVersionBool = true;
            $isLCI = true;
            $lciHwType = 'NBT';
            $gdBool = true;
        } elseif (preg_match($patternLCI_EVO, $hwVersion)) {
            $hwVersionBool = true;
            $isLCI = true;
            $lciHwType = 'EVO';
            $gdBool = true;
        }

        if (!$hwVersionBool) {
            return response()->json(['msg' => 'There was a problem identifying your software. Contact us for help.'], 200);
        }

        if (str_starts_with(strtolower($version), 'v')) {
            $version = substr($version, 1);
        }

        $softwareVersions = SoftwareVersion::all();
        $response = [];

        foreach ($softwareVersions as $row) {
            if (strcasecmp($row->system_version_alt, $version) === 0) {
                $isLCIEntry = (str_starts_with($row->name, 'LCI'));

                if ($isLCI !== $isLCIEntry) continue;
                if ($isLCI && stripos($row->name, $lciHwType) === false) continue;

                if ($row->latest) {
                    $response = ['versionExist' => true, 'msg' => 'Your system is upto date!', 'link' => '', 'st' => '', 'gd' => ''];
                } else {
                    $stLink = $stBool ? $row->st : '';
                    $gdLink = $gdBool ? $row->gd : '';
                    $latestMsg = $isLCI ? 'v3.4.4' : 'v3.3.7';
                    $response = [
                        'versionExist' => true, 
                        'msg' => 'The latest version of software is ' . $latestMsg . ' ', 
                        'link' => $row->link, 
                        'st' => $stLink, 
                        'gd' => $gdLink
                    ];
                }
                break;
            }
        }

        if ($response) return response()->json($response, 200);

        return response()->json([
            'versionExist' => false, 
            'msg' => 'There was a problem identifying your software. Contact us for help.', 
            'link' => '', 'st' => '', 'gd' => ''
        ], 200);
    }
}