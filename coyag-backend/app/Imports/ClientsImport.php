<?php

namespace App\Imports;

use App\Models\AddedService;
use App\Models\Client;
use App\Models\User;
use App\Services\ClientService;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\{Importable, ToModel, WithHeadingRow, WithValidation};

class ClientsImport implements ToModel, WithHeadingRow, WithValidation
{

    /**
     * Crea los clientes desde el archivo de excel
     */

    use Importable;

    private $currentRow;
    private $arrReturn;
    private ClientService $clientService;

    public function __construct(array &$arrReturn)
    {
        $this->arrReturn = &$arrReturn;
        $this->currentRow = 2;
        $this->clientService = new ClientService();
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */

    public function model(array $row)
    {
        Log::info('--------------------------');

        if(empty($row['email']))
        {
            $this->writeError('Sin campo Correo electrónico', "Fila: {$this->currentRow}");
            return null;
        }

        $user = User::where('email', $row['email'])->first();
        $phoneMobile = empty($row['phone_number'])      ? null
                                                        : str_replace([' ', '=', 'p', ':', '+'], '', $row['phone_number']);

        $client;

        if(!empty($user))
        {
            $client = Client::find($user->client->id);

            $aux = AddedService::leftJoin('services', 'services.id', '=', 'added_services.service_id')
                                ->where('services.type', 'Plan')
                                ->where('added_services.client_id', $client->id)
                                ->count();

            if($aux > 0)
            {
                $this->writeError("No aplica, {$row['email']} ya ha tenido o tiene un plan de VideoPortal", $client->user->email);
                return null;
            }

            // Elimina algún Plan anterior y agrega el FreeTrial
            $this->clientService->addFreeTrial($client);

            Log::info("Editado el cliente {$row['email']}");
        }
        else
        {
            $client = $this->clientService->store([
                'email'         =>  strtolower($row['email']),
                'password'      => $phoneMobile
            ], true);

            if(empty($client))
            {
                $this->writeError('No se pudo crear el cliente', strtolower($row['email']));
                return null;
            }

            Log::info("Creado el cliente {$row['email']}");
        }

        $client->names =  empty($row['full_name'])      ? null
                                                        : $row['full_name'];

        // TODO: Próxima carga debería estar separado el campo apellido
        // $client->surnames = empty($row['apellidos']) ? null : $row['apellidos'];


        $client->phone_mobile = '+' . $phoneMobile;
        $client->registration_method = 'Carga Masiva';

        $client->save();

        $this->arrReturn['welcome_email'] .= $this->arrReturn['welcome_email'] == ''    ? $client->user->email
                                                                                        : ",{$client->user->email}";

        Log::info('--------------------------');

        $this->arrReturn['loaded_rows'] .= ($this->arrReturn['loaded_rows'] == '')  ? $this->currentRow : ",{$this->currentRow}";
        $this->arrReturn['highest_row'] = $this->currentRow;
        $this->currentRow++;

        return $client;
    }


    public function rules() : array
    {
        return [
            //'name'    => Rule::in(['max:64']),
            //'price'     => Rule::in(['numeric']),
        ];
    }


    private function writeError(string $message, string $notLoaded)
    {
        $this->arrReturn['unloaded_rows'] .= ($this->arrReturn['unloaded_rows'] == '')  ? $this->currentRow : ",{$this->currentRow}";
        $this->arrReturn['highest_row'] = $this->currentRow;
        Log::info("$message - Fila: {$this->currentRow} del excel");

        $this->arrReturn['not_loaded'] .= $this->arrReturn['not_loaded'] == ''  ? $notLoaded
                                                                                : ",$notLoaded";

        $this->currentRow++;
        Log::info('--------------------------');
    }

}
