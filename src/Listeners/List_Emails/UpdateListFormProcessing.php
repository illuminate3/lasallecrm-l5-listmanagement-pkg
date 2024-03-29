<?php
namespace Lasallecrm\Listmanagement\Listeners\List_Emails;

/**
 *
 * List Management package for the LaSalle Customer Relationship Management package.
 *
 * Based on the Laravel 5 Framework.
 *
 * Copyright (C) 2015  The South LaSalle Trading Corporation
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 *
 * @package    List Management package for the LaSalle Customer Relationship Management package
 * @link       http://LaSalleCRM.com
 * @copyright  (c) 2015, The South LaSalle Trading Corporation
 * @license    http://www.gnu.org/licenses/gpl-3.0.html
 * @author     The South LaSalle Trading Corporation
 * @email      info@southlasalle.com
 *
 */

///////////////////////////////////////////////////////////////////
///            THIS IS A COMMAND HANDLER                        ///
///////////////////////////////////////////////////////////////////


///////////////////////////////////////////////////////////////////
///  NOTE: THE REPOSITORY IS THE BASE REPOSITORY, NOT A         ///
///  REPOSITORY SPECIFIC TO THE MODEL. THE REASON IS TO         ///
///  FACILITATE AUTOMATION OF ADMIN FORMS. YOU CAN ALWAYS       ///
///  DO A MODEL-SPECIFIC REPOSITORY IF NEED BE.                 ///
///////////////////////////////////////////////////////////////////


// LaSalle Software
use Lasallecms\Lasallecmsapi\Repositories\BaseRepository;
use Lasallecms\Lasallecmsapi\FormProcessing\BaseFormProcessing;

/*
 * Process an existing record.
 *
 * FYI: BaseFormProcessing implements the FormProcessing interface.
 */
class UpdateList_EmailFormProcessing extends BaseFormProcessing
{
    /*
     * Instance of repository
     *
     * @var Lasallecms\Lasallecmsapi\Repositories\BaseRepository
     */
    protected $repository;


    ///////////////////////////////////////////////////////////////////
    /// SPECIFY THE TYPE OF PERSIST THAT IS GOING ON HERE:          ///
    ///  * "create"  for INSERT                                     ///
    ///  * "update   for UPDATE                                     ///
    ///  * "destroy" for DELETE                                     ///
    ///////////////////////////////////////////////////////////////////
    /*
     * Type of persist
     *
     * @var string
     */
    protected $type = "update";

    ///////////////////////////////////////////////////////////////////
    /// SPECIFY THE FULL NAMESPACE AND CLASS NAME OF THE MODEL      ///
    ///////////////////////////////////////////////////////////////////
    /*
     * Namespace and class name of the model
     *
     * @var string
     */
    protected $namespaceClassnameModel = "Lasallecrm\Listmanagement\Models\List_Email";



    ///////////////////////////////////////////////////////////////////
    ///   USUALLY THERE IS NOTHING ELSE TO MODIFY FROM HERE ON IN   ///
    ///////////////////////////////////////////////////////////////////


    /*
     * Inject the model
     *
     * @param Lasallecms\Lasallecmsapi\Repositories\BaseRepository
     */
    public function __construct(BaseRepository $repository)
    {
        $this->repository = $repository;

        $this->repository->injectModelIntoRepository($this->namespaceClassnameModel);
    }

    /*
     * The form processing steps.
     *
     * @param  object  $createCommand   The command bus object
     * @return array                    The custom response array
     */
    public function quarterback($updateCommand)
    {
        // Convert the command bus object into an array
        $data = (array) $updateCommand;


        // Sanitize
        $data = $this->sanitize($data, $this->type);


        // Validate
        if ($this->validate($data, $this->type) != "passed")
        {
            // Unlock the record
            $this->unlock($data['id']);

            // Prepare the response array, and then return to the edit form with error messages
            return $this->prepareResponseArray('validation_failed', 500, $data, $this->validate($data, $this->type));
        }


        // Even though we already sanitized the data, we further "wash" the data
        $data = $this->wash($data);


        // UPDATE record
        if (!$this->persist($data, $this->type))
        {
            // Unlock the record
            $this->unlock($data['id']);

            // Prepare the response array, and then return to the edit form with error messages
            // Laravel's https://github.com/laravel/framework/blob/5.0/src/Illuminate/Database/Eloquent/Model.php
            //  does not prepare a MessageBag object, so we'll whip up an error message in the
            //  originating controller
            return $this->prepareResponseArray('persist_failed', 500, $data);
        }


        // Unlock the record
        $this->unlock($data['id']);


        // Prepare the response array, and then return to the command
        return $this->prepareResponseArray('update_successful', 200, $data);


        ///////////////////////////////////////////////////////////////////
        ///     NO EVENTS ARE SPECIFIED IN THE BASE FORM PROCESSING     ///
        ///////////////////////////////////////////////////////////////////
    }
}