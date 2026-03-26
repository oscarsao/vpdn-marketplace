<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServicesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Service::create([
            'name'                          =>  'Plan Registrado',
            'slug'                          =>  'plan.registrado',
            'type'                          =>  'Plan',
            'roles_slug'                    =>  'cliente.registrado',
            'name_payment'                  =>  'Plan Registrado No Aplica Pago',
            'slug_payment'                  =>  'plan.registrado.no.aplica.pago',
            'flag_recurring_payment'        =>  false,
            'flag_payment_in_installments'  =>  false,
            'financial_analysis_available'  =>  0,
            'flag_mandatory_payment'        =>  0,
            'flag_active'                   =>  false,
        ]);

        Service::create([
            'name'                          =>  'Plan Lite',
            'slug'                          =>  'plan.lite',
            'type'                          =>  'Plan',
            'roles_slug'                    =>  'usuario.lite',
            'name_payment'                  =>  'Mensualidad Plan Lite',
            'slug_payment'                  =>  'mensualidad.plan.lite',
            'recommended_price'             =>  '100€ al mes',
            'flag_recurring_payment'        =>  true,
            'flag_payment_in_installments'  =>  false,
            'financial_analysis_available'  =>  0,
            'flag_mandatory_payment'        =>  1
        ]);

        Service::create([
            'name'                          =>  'Plan Estándar mayor a 6 meses',
            'slug'                          =>  'plan.estandar.mayor',
            'type'                          =>  'Plan',
            'roles_slug'                    =>  'usuario.estandar.mayor',
            'name_payment'                  =>  'Pago Plan Estándar mayor a 6 meses',
            'slug_payment'                  =>  'pago.plan.estandar.mayor',
            'recommended_price'             =>  '6% o 4500 euros mínimo',
            'flag_recurring_payment'        =>  false,
            'flag_payment_in_installments'  =>  true,
            'financial_analysis_available'  =>  2,
            'flag_mandatory_payment'        =>  1
        ]);

        Service::create([
            'name'                          =>  'Plan Estándar menor a 6 meses',
            'slug'                          =>  'plan.estandar.menor',
            'type'                          =>  'Plan',
            'roles_slug'                    =>  'usuario.estandar.menor',
            'name_payment'                  =>  'Pago Plan Estándar menor a 6 meses',
            'slug_payment'                  =>  'pago.plan.estandar.menor',
            'recommended_price'             =>  '10% o 7500 Euros mínimo + IVA',
            'flag_recurring_payment'        =>  false,
            'flag_payment_in_installments'  =>  true,
            'financial_analysis_available'  =>  1,
            'flag_mandatory_payment'        =>  1
        ]);

        Service::create([
            'name'                          =>  'Plan Premium mayor a 6 meses',
            'slug'                          =>  'plan.premium.mayor',
            'type'                          =>  'Plan',
            'roles_slug'                    =>  'usuario.premium.mayor',
            'name_payment'                  =>  'Pago Plan Premium mayor a 6 meses',
            'slug_payment'                  =>  'pago.plan.premium.mayor',
            'recommended_price'             =>  '8% o 6000 euros mínimo',
            'flag_recurring_payment'        =>  false,
            'flag_payment_in_installments'  =>  true,
            'financial_analysis_available'  =>  -1,
            'flag_mandatory_payment'        =>  1
        ]);

        Service::create([
            'name'                          =>  'Plan Premium menor a 6 meses',
            'slug'                          =>  'plan.premium.menor',
            'type'                          =>  'Plan',
            'roles_slug'                    =>  'usuario.premium.menor',
            'name_payment'                  =>  'Pago Plan Premium menor a 6 meses',
            'slug_payment'                  =>  'pago.plan.premium.menor',
            'recommended_price'             =>  '12% o 9000 Euros mínimo + IVA',
            'flag_recurring_payment'        =>  false,
            'flag_payment_in_installments'  =>  true,
            'financial_analysis_available'  =>  3,
            'flag_mandatory_payment'        =>  1
        ]);

        Service::create([
            'name'                          =>  'Análisis Financiero-Jurídico - Plan Lite',
            'slug'                          =>  'analisis.financiero.juridico.plan.lite',
            'type'                          =>  'AddOn',
            'roles_slug'                    =>  'usuario.lite',
            'name_payment'                  =>  'Análisis Financiero-Jurídico - Plan Lite',
            'slug_payment'                  =>  'analisis.financiero.juridico.plan.lite',
            'recommended_price'             =>  '500€',
            'flag_recurring_payment'        =>  false,
            'flag_payment_in_installments'  =>  false,
            'financial_analysis_available'  =>  1,
            'flag_mandatory_payment'        =>  1
        ]);

        Service::create([
            'name'                          =>  'Análisis Financiero-Jurídico X1 - Plan Estándar',
            'slug'                          =>  'analisis.financiero.juridico.x1.plan.estandar',
            'type'                          =>  'AddOn',
            'roles_slug'                    =>  'usuario.estandar.mayor,usuario.estandar.menor',
            'name_payment'                  =>  'Análisis Financiero-Jurídico X1 - Plan Estándar',
            'slug_payment'                  =>  'analisis.financiero.juridico.x1.plan.estandar',
            'recommended_price'             =>  '1 x 1.500€',
            'flag_recurring_payment'        =>  false,
            'flag_payment_in_installments'  =>  false,
            'financial_analysis_available'  =>  1,
            'flag_mandatory_payment'        =>  1
        ]);

        Service::create([
            'name'                          =>  'Análisis Financiero-Jurídico X3 - Plan Estándar',
            'slug'                          =>  'analisis.financiero.juridico.x3.plan.estandar',
            'type'                          =>  'AddOn',
            'roles_slug'                    =>  'usuario.estandar.mayor,usuario.estandar.menor',
            'name_payment'                  =>  'Análisis Financiero-Jurídico X3 - Plan Estándar',
            'slug_payment'                  =>  'analisis.financiero.juridico.x3.plan.estandar',
            'recommended_price'             =>  '3 x 1.200€ c/estudio',
            'flag_recurring_payment'        =>  false,
            'flag_payment_in_installments'  =>  false,
            'financial_analysis_available'  =>  3,
            'flag_mandatory_payment'        =>  1
        ]);

        Service::create([
            'name'                          =>  'Análisis Financiero-Jurídico X5 - Plan Estándar',
            'slug'                          =>  'analisis.financiero.juridico.x5.plan.estandar',
            'type'                          =>  'AddOn',
            'roles_slug'                    =>  'usuario.estandar.mayor,usuario.estandar.menor',
            'name_payment'                  =>  'Análisis Financiero-Jurídico X5 - Plan Estándar',
            'slug_payment'                  =>  'analisis.financiero.juridico.x5.plan.estandar',
            'recommended_price'             =>  '5 x 1.000€ c/estudio',
            'flag_recurring_payment'        =>  false,
            'flag_payment_in_installments'  =>  false,
            'financial_analysis_available'  =>  5,
            'flag_mandatory_payment'        =>  1
        ]);

        Service::create([
            'name'                          =>  'Análisis Financiero-Jurídico X1 - Plan Premium',
            'slug'                          =>  'analisis.financiero.juridico.x1.plan.premium',
            'type'                          =>  'AddOn',
            'roles_slug'                    =>  'usuario.premium.menor',
            'name_payment'                  =>  'Análisis Financiero-Jurídico X1 - Plan Premium',
            'slug_payment'                  =>  'analisis.financiero.juridico.x1.plan.premium',
            'recommended_price'             =>  '1 x 1.500€',
            'flag_recurring_payment'        =>  false,
            'flag_payment_in_installments'  =>  false,
            'financial_analysis_available'  =>  1,
            'flag_mandatory_payment'        =>  1
        ]);

        Service::create([
            'name'                          =>  'Análisis Financiero-Jurídico X3 - Plan Premium',
            'slug'                          =>  'analisis.financiero.juridico.x3.plan.premium',
            'type'                          =>  'AddOn',
            'roles_slug'                    =>  'usuario.premium.menor',
            'name_payment'                  =>  'Análisis Financiero-Jurídico X3 - Plan Premium',
            'slug_payment'                  =>  'analisis.financiero.juridico.x3.plan.premium',
            'recommended_price'             =>  '3 x 1.200€ c/estudio',
            'flag_recurring_payment'        =>  false,
            'flag_payment_in_installments'  =>  false,
            'financial_analysis_available'  =>  3,
            'flag_mandatory_payment'        =>  1
        ]);

        Service::create([
            'name'                          =>  'Análisis Financiero-Jurídico X5 - Plan Premium',
            'slug'                          =>  'analisis.financiero.juridico.x5.plan.premium',
            'type'                          =>  'AddOn',
            'roles_slug'                    =>  'usuario.premium.menor',
            'name_payment'                  =>  'Análisis Financiero-Jurídico X5 - Plan Premium',
            'slug_payment'                  =>  'analisis.financiero.juridico.x5.plan.premium',
            'recommended_price'             =>  '5 x 1.000€ c/estudio',
            'flag_recurring_payment'        =>  false,
            'flag_payment_in_installments'  =>  false,
            'financial_analysis_available'  =>  5,
            'flag_mandatory_payment'        =>  1
        ]);

       Service::create([
           'name'                          =>  'Plan Fase de Evaluación',
           'slug'                          =>  'plan.fase.evaluacion',
           'type'                          =>  'Plan',
           'roles_slug'                    =>  'cliente.fase.evaluacion',
           'name_payment'                  =>  'Pago Plan Fase de Evaluación',
           'slug_payment'                  =>  'pago.plan.fase.evaluacion',
           'recommended_price'             =>  '1000€ al año',
           'flag_recurring_payment'        =>  false,
           'flag_payment_in_installments'  =>  false,
           'financial_analysis_available'  =>  0,
           'flag_mandatory_payment'        =>  true
       ]);

       Service::create([
           'name'                          =>  'Plan Fase de Análisis',
           'slug'                          =>  'plan.fase.analisis',
           'type'                          =>  'Plan',
           'roles_slug'                    =>  'cliente.fase.analisis',
           'name_payment'                  =>  'Pago Plan Fase de Análisis',
           'slug_payment'                  =>  'pago.plan.fase.analisis',
           'recommended_price'             =>  '2000€ al año',
           'flag_recurring_payment'        =>  false,
           'flag_payment_in_installments'  =>  false,
           'financial_analysis_available'  =>  2,
           'flag_mandatory_payment'        =>  true
       ]);

       Service::create([
           'name'                          =>  'Plan Fase de Ejecución',
           'slug'                          =>  'plan.fase.ejecucion',
           'type'                          =>  'Plan',
           'roles_slug'                    =>  'cliente.fase.ejecucion',
           'name_payment'                  =>  'Pago Plan Fase de Ejecución',
           'slug_payment'                  =>  'pago.plan.fase.ejecucion',
           'recommended_price'             =>  '3000€ al año',
           'flag_recurring_payment'        =>  false,
           'flag_payment_in_installments'  =>  false,
           'financial_analysis_available'  =>  0,
           'flag_mandatory_payment'        =>  true
       ]);

       Service::create([
           'name'                          =>  'Plan Fase de Asesoramiento Integral',
           'slug'                          =>  'plan.fase.asesoramiento.integral',
           'type'                          =>  'Plan',
           'roles_slug'                    =>  'cliente.fase.asesoramiento.integral',
           'name_payment'                  =>  'Pago Plan Fase de Asesoramiento Integral',
           'slug_payment'                  =>  'pago.plan.fase.asesoramiento.integral',
           'recommended_price'             =>  '4000€ al año',
           'flag_recurring_payment'        =>  false,
           'flag_payment_in_installments'  =>  false,
           'financial_analysis_available'  =>  0,
           'flag_mandatory_payment'        =>  true
       ]);

        Service::create([
            'name'                          =>  'Análisis Financiero-Jurídico X1 - Plan Fase de Análisis',
            'slug'                          =>  'analisis.financiero.juridico.x1.plan.fase.analisis',
            'type'                          =>  'AddOn',
            'roles_slug'                    =>  'cliente.fase.analisis',
            'name_payment'                  =>  'Análisis Financiero-Jurídico X1 - Plan Fase de Análisis',
            'slug_payment'                  =>  'analisis.financiero.juridico.x1.plan.fase.analisis',
            'recommended_price'             =>  'Sin precio recomendado',
            'flag_recurring_payment'        =>  false,
            'flag_payment_in_installments'  =>  false,
            'financial_analysis_available'  =>  1,
            'flag_mandatory_payment'        =>  true
        ]);

        Service::create([
            'name'                          =>  'Extranjería Primera Residencia',
            'slug'                          =>  'extranjeria.primera.residencia',
            'type'                          =>  'Inmigration',
            'roles_slug'                    =>  'cliente.extranjeria.primera.residencia',
            'name_payment'                  =>  'Pago Extranjería Primera Residencia',
            'slug_payment'                  =>  'pago.extranjeria.primera.residencia',
            'recommended_price'             =>  'N/A',
            'flag_recurring_payment'        =>  false,
            'flag_payment_in_installments'  =>  false,
            'financial_analysis_available'  =>  0,
            'flag_mandatory_payment'        =>  true
        ]);

        Service::create([
            'name'                          =>  'Extranjería Renovación de Residencia',
            'slug'                          =>  'extranjeria.renovacion.primera.residencia',
            'type'                          =>  'Inmigration',
            'roles_slug'                    =>  'cliente.extranjeria.renovacion.residencia',
            'name_payment'                  =>  'Pago Extranjería renovación de Residencia',
            'slug_payment'                  =>  'pago.extranjeria.renovacion.primera.residencia',
            'recommended_price'             =>  'N/A',
            'flag_recurring_payment'        =>  false,
            'flag_payment_in_installments'  =>  false,
            'financial_analysis_available'  =>  0,
            'flag_mandatory_payment'        =>  true
        ]);

        Service::create([
            'name'                          =>  'Extranjería Ciudadanía',
            'slug'                          =>  'extranjeria.ciudadania',
            'type'                          =>  'Inmigration',
            'roles_slug'                    =>  'cliente.extranjeria.ciudadania',
            'name_payment'                  =>  'Pago Extranjería Ciudadanía',
            'slug_payment'                  =>  'pago.extranjeria.ciudadania',
            'recommended_price'             =>  'N/A',
            'flag_recurring_payment'        =>  false,
            'flag_payment_in_installments'  =>  false,
            'financial_analysis_available'  =>  0,
            'flag_mandatory_payment'        =>  true
        ]);


    }
}
