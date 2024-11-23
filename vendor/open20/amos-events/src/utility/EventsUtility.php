<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\events\utility
 * @category   CategoryName
 */

namespace open20\amos\events\utility;

use dosamigos\qrcode\lib\Enum;
use dosamigos\qrcode\QrCode;
use open20\amos\attachments\FileModule;
use open20\amos\attachments\models\File;
use open20\amos\community\AmosCommunity;
use open20\amos\community\exceptions\CommunityException;
use open20\amos\community\models\Community;
use open20\amos\community\models\CommunityAmosWidgetsMm;
use open20\amos\community\models\CommunityContextInterface;
use open20\amos\community\models\CommunityType;
use open20\amos\community\models\CommunityUserMm;
use open20\amos\community\utilities\CommunityUtil;
use open20\amos\core\forms\editors\DateTime;
use open20\amos\core\helpers\Html;
use open20\amos\core\icons\AmosIcons;
use open20\amos\core\user\User;
use open20\amos\core\utilities\Email;
use open20\amos\events\AmosEvents;
use open20\amos\events\assets\WizardEventAsset;
use open20\amos\events\models\base\EventCommunication;
use open20\amos\events\models\base\EventInvitationSpecificUsers;
use open20\amos\events\models\Event;
use open20\amos\events\models\EventAccreditationList;
use open20\amos\events\models\EventEmailTemplates;
use open20\amos\events\models\EventGroupReferent;
use open20\amos\events\models\EventGroupReferentMm;
use open20\amos\events\models\EventInactiveUsersMailup;
use open20\amos\events\models\EventInternalList;
use open20\amos\events\models\EventInvitation;
use open20\amos\events\models\EventInvitationSent;
use open20\amos\events\models\EventLanding;
use open20\amos\events\models\EventLandingDocuments;
use open20\amos\events\models\EventLandingNews;
use open20\amos\events\models\EventLandingProtagonist;
use open20\amos\events\models\EventLocation;
use open20\amos\events\models\EventLocationEntrances;
use open20\amos\events\models\EventParticipantCompanion;
use open20\amos\events\models\EventRelated;
use open20\amos\events\models\EventSeats;
use open20\amos\events\models\EventType;
use open20\amos\invitations\models\Invitation;
use open20\amos\seo\models\SeoData;
use open20\amos\tag\models\EntitysTagsMm;
use open20\amos\tag\models\Tag;
use kartik\mpdf\Pdf;
use luya\web\filters\ResponseCache;
use Yii;
use yii\base\Exception;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\httpclient\Client;
use yii\log\Logger;
use yii\web\ForbiddenHttpException;

/**
 * Class EventsUtility
 * @package open20\amos\events\utility
 */
class EventsUtility
{
    /**
     * This method translate the array values.
     * @param array $arrayValues
     * @return array
     */
    public static function translateArrayValues($arrayValues)
    {
        $translatedArrayValues = [];
        foreach ($arrayValues as $key => $title) {
            $translatedArrayValues[$key] = AmosEvents::t('amosevents', $title);
        }
        return $translatedArrayValues;
    }

    /**
     * @param $model
     * @param string $managerStatus
     * @param null $typeCommunity
     * @return bool
     */
    public static function createCommunity($model, $managerStatus = '', $typeCommunity = null)
    {
        /** @var AmosCommunity $communityModule */
        $communityModule = Yii::$app->getModule('community');
        $title = ($model->title ? $model->title : '');
        $description = ($model->description ? $model->description : '');
        $eventType = $model->eventType;
        $type = CommunityType::COMMUNITY_TYPE_CLOSED; // DEFAULT TYPE

        if ($typeCommunity) {
            $type = $typeCommunity;
        } else {
            if (!is_null($eventType) && $eventType->event_type == EventType::TYPE_OPEN) {
                $type = CommunityType::COMMUNITY_TYPE_OPEN;
            } else if (!is_null($eventType) && $eventType->event_type == EventType::TYPE_UPON_INVITATION) {
                $type = CommunityType::COMMUNITY_TYPE_CLOSED;
            }
        }

        $context = AmosEvents::instance()->model('Event');
        $managerRole = $model->getManagerRole();
        try {
            $model->community_id = $communityModule->createCommunity(
                $title,
                $type,
                $context,
                $managerRole,
                $description,
                $model,
                $managerStatus
            );
            $ok = $model->save(false);
            if (!is_null($model->community_id)) {
                $ok = EventsUtility::duplicateEventTagForCommunity($model);
            }
        } catch (CommunityException $exception) {
            \Yii::getLogger()->log($exception->getMessage(), Logger::LEVEL_ERROR);
            $ok = false;
        }
        return $ok;
    }

    /**
     * Update a community.
     * @param Event $model
     * @return bool
     */
    public static function updateCommunity($model)
    {
        $model->community->name = $model->title;
        $model->community->description = $model->description;
        $ok = $model->community->save(false);
        return $ok;
    }

    /**
     * @param Event $model
     * @return bool
     */
    public static function duplicateEventTagForCommunity($model)
    {
        $moduleTag = Yii::$app->getModule('tag');
        /** @var AmosEvents $eventsModule */
        $eventsModule = AmosEvents::instance();
        $ok = true;
        if (isset($moduleTag) && in_array(
                $eventsModule->model('Event'),
                $moduleTag->modelsEnabled
            ) && $moduleTag->behaviors) {
            $eventTags = \open20\amos\tag\models\EntitysTagsMm::findAll([
                'classname' => $eventsModule->model('Event'),
                'record_id' => $model->id
            ]);
            foreach ($eventTags as $eventTag) {
                $entityTag = new \open20\amos\tag\models\EntitysTagsMm();
                $entityTag->classname = Community::className();
                $entityTag->record_id = $model->community_id;
                $entityTag->tag_id = $eventTag->tag_id;
                $entityTag->root_id = $eventTag->root_id;
                $ok = $entityTag->save(false);
                if (!$ok) {
                    break;
                }
            }
        }
        return $ok;
    }

    /**
     * @param Event $model
     * @return bool
     */
    public static function duplicateEventLogoForCommunity($model)
    {
        $ok = true;
        $eventLogo = File::findOne([
            'model' => AmosEvents::instance()->model('Event'), 'attribute' => 'eventLogo',
            'itemId' => $model->id
        ]);
        if (!is_null($eventLogo)) {
            $communityLogo = File::findOne([
                'model' => Community::className(), 'attribute' => 'communityLogo',
                'itemId' => $model->community_id
            ]);
            if (!is_null($communityLogo)) {
                if ($eventLogo->hash != $communityLogo->hash) {
                    $communityLogo->delete();
                    $ok = EventsUtility::newCommunityLogo(
                        $model->community_id,
                        $eventLogo
                    );
                }
            } else {
                $ok = EventsUtility::newCommunityLogo(
                    $model->community_id,
                    $eventLogo
                );
            }
        } else {
            $communityLogo = File::findOne([
                'model' => Community::className(), 'attribute' => 'communityLogo',
                'itemId' => $model->community_id
            ]);
            if (!is_null($communityLogo)) {
                $communityLogo->delete();
            }
        }
        return $ok;
    }

    /**
     * @param int $communityId
     * @param File $eventLogo
     * @return bool
     */
    private static function newCommunityLogo($communityId, $eventLogo)
    {
        $communityLogo = new File();
        $eventLogoAttributes = $eventLogo->attributes;
        $toSkipFields = ['id', 'model', 'attribute', 'itemId'];
        foreach ($eventLogoAttributes as $fieldName => $fieldValue) {
            if (!in_array($fieldName, $toSkipFields)) {
                $communityLogo->{$fieldName} = $fieldValue;
            }
        }
        $communityLogo->model = Community::className();
        $communityLogo->attribute = 'communityLogo';
        $communityLogo->itemId = $communityId;
        return $communityLogo->save();
    }

    public static function deleteCommunityLogo($model)
    {
        $communityLogo = File::findOne([
            'model' => Community::className(), 'attribute' => 'communityLogo',
            'itemId' => $model->community_id
        ]);
        if (!is_null($communityLogo)) {
            $communityLogo->delete();
        }
    }

    /**
     * Check if there is at least one confirmed event manager only if there is a community. If not it return true.
     * @param Event $event
     * @return bool
     */
    public static function checkOneConfirmedManagerPresence($event)
    {
        if (!$event->community_id) {
            return true;
        }
        $confirmedEventManagers = self::findEventManagers(
            $event,
            CommunityUserMm::STATUS_ACTIVE
        );
        return (count($confirmedEventManagers) > 0);
    }

    /**
     * Check if there is at least one confirmed event manager only if there is a community. If not it return true.
     * @param Event $event
     * @param string $status
     * @return array[CommunityUserMm]
     */
    public static function findEventManagers($event, $status = '')
    {
        if (!$event->community_id) {
            return [];
        }

        $where = [
            'community_id' => $event->community_id,
            'role' => $event->getManagerRole()
        ];

        if ($status) {
            $where['status'] = $status;
        }

        $eventManagers = CommunityUserMm::find()->andWhere($where)->all();

        return $eventManagers;
    }

    /**
     * @param null|integer $userId
     * @return array|null|\yii\db\ActiveRecord
     */
    public static function getUserCalendarService($userId = null)
    {
        $socialAuth = \Yii::$app->getModule('socialauth');
        if (!is_null($socialAuth)) {
            if (is_null($userId)) {
                $userId = \Yii::$app->user->id;
            }
            $socialAuthUser = \open20\amos\socialauth\models\SocialAuthUsers::findOne([
                'user_id' => $userId, 'provider' => 'google'
            ]);
            if (!is_null($socialAuthUser)) {
                $service = $socialAuthUser->getServices()->andWhere(['service' => 'calendar'])->one();
                return $service;
            }
        }
        return null;
    }

    /**
     * @param null|\open20\amos\socialauth\models\SocialAuthServices $service
     * @return \Google_Service_Calendar|null
     */
    public static function getGoogleServiceCalendar($service = null)
    {
        $socialAuth = \Yii::$app->getModule('socialauth');
        if (!is_null($socialAuth)) {
            $client = $socialAuth->getClient('google', $service);
            if (!is_null($client)) {
                return new \Google_Service_Calendar($client);
            }
        }
        return null;
    }

    /**
     * @param \Google_Service_Calendar $serviceGoogle
     * @param string $calendarId
     * @param \Google_Service_Calendar_Event $eventCalendar
     * @return bool - operation result
     */
    public static function insertOrUpdateGoogleEvent(
        $serviceGoogle,
        $calendarId,
        $eventCalendar
    )
    {

        $eventId = $eventCalendar->getId();
        try {
            $eventCalendarExists = $serviceGoogle->events->get(
                $calendarId,
                $eventId
            );
            $isUpdate = true;
        } catch (\Google_Service_Exception $ex) {
            $isUpdate = false;
        }
        try {
            if (!$isUpdate) {
                $serviceGoogle->events->insert($calendarId, $eventCalendar);
            } else {
                $serviceGoogle->events->update(
                    $calendarId,
                    $eventCalendar->getId(),
                    $eventCalendar
                );
            }
        } catch (\Google_Service_Exception $e) {
            Yii::getLogger()->log(
                'Google calendar insert or update event ' . $eventId . ': ' . $e->getMessage(),
                Logger::LEVEL_WARNING
            );
            return false;
        }
        return true;
    }

    /**
     * @param \Google_Service_Calendar $serviceGoogle
     * @param string $calendarId
     * @param string $eventId
     * @return bool - operation result
     */
    public static function deleteGoogleEvent(
        $serviceGoogle,
        $calendarId,
        $eventId
    )
    {

        try {
            $eventCalendar = $serviceGoogle->events->get($calendarId, $eventId);
        } catch (\Google_Service_Exception $ex) {
            return true;
        }
        try {
            $serviceGoogle->events->delete($calendarId, $eventId);
        } catch (\Google_Service_Exception $e) {
            return false;
        }
        return true;
    }

    /**
     * @param Event $event
     * @param int $eventId
     * @return int|string
     */
    public static function cmpSeatsAvailable(Event $event, $eventId = 0)
    {
        $count = 0;
        try {
            if (is_null($event)) {
                if ($eventId > 0) {
                    /** @var AmosEvents $eventsModule */
                    $eventsModule = AmosEvents::instance();
                    /** @var Event $eventModel */
                    $eventModel = $eventsModule->createModel('Event');
                    $event = $eventModel::findOne($eventId);
                } else {
                    throw new Exception('No event present');
                }
            }
            $community = $event->getCommunityModel();
            $query = $community->getCommunityManagers();
            $members = $query->count();
            $count = $event->seats_available - $members;
        } catch (\Exception $ex) {
            \Yii::getLogger()->log($ex->getMessage(), Logger::LEVEL_ERROR);
        }

        return $count;
    }

    public static function checkManager($event)
    {
        $communityUtil = new CommunityUtil();

        if ($communityUtil->isManagerLoggedUser($event)) {
            return true;
        }

        $isOperator = EventsUtility::isUserEventOperator(\Yii::$app->user->id, $event);

        return $isOperator;
    }

    /**
     * @param CommunityContextInterface $model
     * @return bool
     */
    public static function hasPrivilegesLoggedUser($model)
    {

        $foundRow = CommunityUserMm::find()->andWhere([
            'community_id' => $model->getCommunityModel()->id,
            'user_id' => \Yii::$app->getUser()->getId(),
            'role' => $model->getPriviledgedRoles()
        ])->limit(1)->count();

        if ($foundRow) return true;

        $isOperator = EventsUtility::isUserEventOperator(\Yii::$app->user->id, $model);

        return $isOperator;
    }

    /**
     * @param Event|null $event
     * @param Invitation|null $invitation
     * @param string $type
     * @param array $companion
     * @param null $url
     * @param string $qrcodeFormat
     * @param int $size
     * @return string
     */
    public static function createQrCode(
        $event = null,
        $invitation = null,
        $type = '',
        $companion = null,
        $url = null,
        $qrcodeFormat = 'png',
        $size = 350
    )
    {
        if ($type == 'participant') {
            if ($event && $invitation) {
                $url = Url::base(true) . Url::toRoute([
                        'register-participant', 'eid' => $event->id,
                        'pid' => (empty($invitation->user_id) ? '' : $invitation->user_id), 'iid' => $invitation->id
                    ]);
            }
        } elseif ($type == 'companion') {
            if ($event && $invitation) {
                $url = Url::base(true) . Url::toRoute([
                        'register-companion', 'eid' => $event->id,
                        'pid' => $invitation->user_id, 'iid' => $invitation->id,
                        'cid' => $companion['id']
                    ]);
            }
        }

        if (!empty($url)) {
            /* if ($qrcodeFormat == 'svg') {
              return QrCode::svg($url, "qrcode", false, Enum::QR_ECLEVEL_M, $size);
              } else */
            if ($qrcodeFormat == 'png') {
                ob_start();
                QrCode::png($url, false, Enum::QR_ECLEVEL_M, $size);
                $imageString = base64_encode(ob_get_contents());
                ob_end_clean();
                return "<img width=\"{$size}\" src=\"data:image/png;base64,{$imageString}\" />";
            }
        }
        return '';
    }

    /**
     * @param $eid
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public static function createDownloadTicket($eid, $user_id = null, $useAttachments = false)
    {
        if (empty($user_id)) {
            $user_id = \Yii::$app->user->id;
        }
        /** @var AmosEvents $eventModule */
        $eventModule = AmosEvents::instance();
        if (!is_null($eventModule)) {
            $temp_dir = $eventModule->getTempPath();
            /** @var Event $eventModel */
            $eventModel = $eventModule->createModel('Event');
            /** @var Event $event */
            $event = $eventModel::findOne(['id' => $eid]);
            $seatModel = null;
            if ($event) {
                $filenameTicket = $eid . '_' . $user_id . '_Ticket.pdf';
                if ($event->has_tickets) {
                    /** @var EventInvitation $eventInvitationModel */
                    $eventInvitationModel = $eventModule->createModel('EventInvitation');
                    /** @var EventParticipantCompanion $eventParticipantCompanionModel */
                    $eventParticipantCompanionModel = $eventModule->createModel('EventParticipantCompanion');
                    /** @var EventInvitation $invitation */
                    $invitation = $eventInvitationModel::find()->andWhere(['event_id' => $eid, 'user_id' => $user_id])->one();
                    if ($invitation) {
                        $companions = $eventParticipantCompanionModel::find()
                            ->andWhere(['event_invitation_id' => $invitation->id])
                            ->all();

                        // get assignd seat
                        $seat = null;
                        if ($event->isSeatManagement()) {
                            $assignedSeat = $invitation->assignedSeat;

                            if ($assignedSeat) {
                                $seat = $assignedSeat->getStringCoordinateSeat();
                                $filenameTicket = $assignedSeat->getTicketName();
                                $seatModel = $assignedSeat;
                            }
                        }

                        $content = \Yii::$app->controller->renderPartial(
                            !empty($event->ticket_layout_view) ? $event->ticket_layout_view
                                : '@vendor/open20/amos-events/src/views/event/pdf-tickets/general-layout',
                            [
                                'eventData' => $event,
                                'participantData' => [
                                    'nome' => $invitation->name,
                                    'cognome' => $invitation->surname,
                                    'azienda' => $invitation->company,
                                    'codice_fiscale' => $event->abilita_codice_fiscale_in_form
                                        ? $invitation->fiscal_code : "",
                                    'email' => $invitation->email,
                                    'note' => $invitation->notes,
                                    'accreditation_list_id' => $invitation->accreditation_list_id,
                                    'accreditationModel' => $invitation->getAccreditationList()->one(),
                                    'companion_of' => null,
                                    'seat' => $seat,

                                ],
                                'seatModel' => $seatModel,
                                'qrcode' => $event->has_qr_code ? EventsUtility::createQrCode(
                                    $event,
                                    $invitation,
                                    'participant',
                                    null,
                                    null,
                                    'png'
                                ) : '',
                            ]
                        );

                        foreach ($companions as $companion) {
                            $seat = null;
                            $seatModel = null;
                            // GET ASSIGNED SEAT
                            if ($event->isSeatManagement()) {
                                $assignedSeat = $companion->assignedSeat;
                                if ($assignedSeat) {
                                    $seat = $assignedSeat->getStringCoordinateSeat();
                                    $seatModel = $assignedSeat;
                                }
                            }
                            $content .= "<pagebreak />";

                            /** @var EventAccreditationList $eventAccreditationListModel */
                            $eventAccreditationListModel = $eventModule->createModel('EventAccreditationList');

                            $content .= \Yii::$app->controller->renderPartial(
                                !empty($event->ticket_layout_view)
                                    ? $event->ticket_layout_view : '@vendor/open20/amos-events/src/views/event/pdf-tickets/general-layout',
                                [
                                    'eventData' => $event,
                                    'participantData' => [
                                        'nome' => $companion->nome,
                                        'cognome' => $companion->cognome,
                                        'azienda' => $companion->azienda,
                                        'codice_fiscale' => $event->abilita_codice_fiscale_in_form
                                            ? $companion->codice_fiscale : "",
                                        'email' => $companion->email,
                                        'note' => $companion->note,
                                        'accreditation_list_id' => $companion->event_accreditation_list_id,
                                        'accreditationModel' => $eventAccreditationListModel::findOne([
                                            'id' => $companion->event_accreditation_list_id
                                        ]),
                                        'companion_of' => $invitation,
                                        'seat' => $seat,

                                    ],
                                    'seatModel' => $seatModel,
                                    'qrcode' => $event->has_qr_code ? EventsUtility::createQrCode(
                                        $event,
                                        $invitation,
                                        'companion',
                                        $companion,
                                        null,
                                        'png'
                                    ) : "",
                                ]
                            );
                        }
                        $filenameTicket = str_replace(" ", "_", $filenameTicket);
                        $pdf = new Pdf([
                            'filename' => $filenameTicket,
                            // set to use core fonts only
                            'mode' => Pdf::MODE_CORE,
                            // A4 paper format
                            'format' => Pdf::FORMAT_A4,
                            // portrait orientation
                            'orientation' => Pdf::ORIENT_PORTRAIT,
                            // stream to browser inline
                            'destination' => Pdf::DEST_BROWSER,
                            // your html content input
                            'content' => $content,
                            'methods' => [
                                //'SetHeader'=>[$event->title],
                                //'SetFooter'=>['{PAGENO}'],
                            ]
                        ]);

                        $pdf->marginBottom = 5;
                        $pdf->marginTop = 5;


                        $pdf_file = $temp_dir . DIRECTORY_SEPARATOR . $filenameTicket . '.pdf';
                        $savepath = $temp_dir . DIRECTORY_SEPARATOR . $filenameTicket . '.jpg';
                        $pdf->output(
                            $pdf->content,
                            $temp_dir . DIRECTORY_SEPARATOR . $filenameTicket . '.pdf',
                            'F'
                        );

                        $ok = exec("convert '" . $pdf_file . "' '" . $savepath . "'");
                        $invitation->ticket_downloaded_at = date("Y-m-d H:i:s");
                        $invitation->ticket_downloaded_by = (!empty(\Yii::$app->user)
                            && !empty(\Yii::$app->user->id)) ? \Yii::$app->user->id
                            : $invitation->user_id;
                        $invitation->save(false);


                        if ($useAttachments) {
                            /** @var  $moduleAttachments FileModule */
                            $moduleAttachments = \Yii::$app->getModule('attachments');
                            if ($moduleAttachments) {
                                $moduleAttachments->attachFile($savepath, $invitation, 'ticketImage');
                                return "ticket_download/" . $filenameTicket . '.jpg';
                            }
                        } else {
                            return "ticket_download/" . $filenameTicket . '.jpg';
                        }
                    } else {
                        return '';
                    }

                    return '';
                } else {
                    return '';
                }
            }
        }
        return '';
    }

    /**
     * This method checks if the user can view the "Enter in community" button in view.
     * @param Event $model
     * @param AmosEvents|null $eventsModule
     * @return bool
     */
    public static function showCommunityButtonInView(Event $model, $eventsModule = null)
    {
        if (is_null($eventsModule)) {
            $eventsModule = AmosEvents::instance();
        }
        return ($eventsModule->enableCommunitySections ||
            (!$eventsModule->enableCommunitySections &&
                EventsUtility::checkManager($model)));
    }

    /**
     * @param $event_id
     * @param $user_id
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public static function isEventParticipant($event_id, $user_id)
    {
        $event = Event::findOne($event_id);
        if ($event) {
            $count = CommunityUserMm::find()
                ->andWhere(['community_id' => $event->community_id])
                ->andWhere(['user_id' => $user_id])
                ->andWhere(['status' => CommunityUserMm::STATUS_ACTIVE])->count();
        }
        return $count;
    }


    /**
     * @param $event_id
     * @param $user_id
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public static function isEventRegisteredToEvent($event_id, $user_id)
    {
        $event = Event::findOne($event_id);
        $count = 0;
        if ($event) {
            $count = EventInvitation::find()
                ->andWhere(['event_id' => $event_id])
                ->andWhere(['user_id' => $user_id])->count();
        }
        return $count;
    }


    /**
     * @param $event_calendars_id
     * @return bool
     * @throws \yii\base\InvalidConfigException
     */
    public static function isLoggedUserPartner($event_calendars_id)
    {
        $count = \open20\amos\events\models\EventCalendars::find()
            ->andWhere(['partner_user_id' => \Yii::$app->user->id])
            ->andWhere(['id' => $event_calendars_id])->count();
        return $count > 0;
    }


    /**
     * @return array|\open20\amos\tag\models\search\TagQuery|mixed
     */
    public static function getPreferenceCenterTags()
    {
        $tags = [];
        $root = Tag::find()
            ->andWhere(['codice' => Event::ROOT_TAG_PREFERENCE_CENTER])->one();

        if ($root) {
            $tags = Tag::find()->andWhere(['root' => $root->id, 'lvl' => 1])
                ->andWhere(['deleted_at' => null])
                ->orderBy('nome ASC')->all();
        }
        return $tags;
    }

    /**
     * @param $model
     * @return array
     */
    public static function getSidebarEvents($model)
    {
        $controllerDashboard = 'event-dashboard';
        $module = \Yii::$app->getModule('events');
        $isEventInformative = ($model->eventType->event_type == \open20\amos\events\models\EventType::TYPE_INFORMATIVE);
        $hasChildren = ($model->getEventAllChildren()->count() > 0);


        if ($model->status != Event::EVENTS_WORKFLOW_STATUS_SUSPENDED) {
            $menu = [
                'dashboard' => [
                    'mainMenu' => [
                        'label' => AmosEvents::t('amosevents', 'Dashboard'),
                        'icon' => '/sprite/material-sprite.svg#ic_dashboard',
                        'activeTargetAction' => 'view',
                        'activeTargetController' => $controllerDashboard,
                        'titleLink' => AmosEvents::t('amosevents', 'Evento'),
                        'url' => '/events/event-dashboard/view?id=' . $model->id
                    ],
                ],
                'info_evento' => [
                    'mainMenu' => [
                        'label' => AmosEvents::t('amosevents', 'Info evento'),
                        'icon' => '/sprite/material-sprite.svg#ic_event',
                        'activeTargetAction' => 'info-event',
                        'activeTargetController' => $controllerDashboard,
                        'titleLink' => AmosEvents::t('amosevents', 'Info evento'),
                        'url' => '/events/event-dashboard/info-event?id=' . $model->id
                    ],
                ],
                'landing' => [
                    'mainMenu' => [
                        'label' => AmosEvents::t('amosevents', 'Landing page'),
                        'icon' => '/sprite/material-sprite.svg#ic_web',
                        'activeTargetAction' => 'landing-settings',
                        'activeTargetController' => $controllerDashboard,
                        'titleLink' => AmosEvents::t('amosevents', 'Landing pages'),
                        'url' => '/events/event-dashboard/landing-settings'
                    ],
                    'dinamicSubMenu' => [
                        'menuLanding1' => [
                            'label' => AmosEvents::t('amosevents', 'Configura'),
                            'url' => '/events/event-dashboard/landing-settings?id=' . $model->id,
                            'activeTargetAction' => 'landing-settings',
                            'activeTargetController' => $controllerDashboard,
                            'titleLink' => AmosEvents::t('amosevents', 'Configura')
                        ],
                        'menuLanding2' => [
                            'label' => AmosEvents::t('amosevents', 'Gestione contenuti'),
                            'url' => '/events/event-dashboard/landing-manage-contents?id=' . $model->id,
                            'activeTargetAction' => 'landing-manage-contents',
                            'activeTargetController' => $controllerDashboard,
                            'titleLink' => AmosEvents::t('amosevents', 'Gestisci contenuti')
                        ],
                    ]
                ],
            ];

            if ($module->enableOrderSection) {
                $menu['landing']['dinamicSubMenu']['menuLanding3'] = [
                    'label' => AmosEvents::t('amosevents', 'Struttura landing'),
                    'url' => '/events/event-landing-section/index?id=' . $model->id,
                    'activeTargetAction' => 'index',
                    'activeTargetController' => 'event-landing-section',
                    'titleLink' => AmosEvents::t('amosevents', 'Struttura landing')
                ];
            }


            if ($model->eventType->webmeeting_webex) {
                $menu['webmeeting'] = [
                    'mainMenu' => [
                        'label' => AmosEvents::t('amosevents', 'Webmeeting Webex'),
                        'icon' => '/sprite/material-sprite.svg#ic_play_circle_outline',
                        'activeTargetAction' => 'web-meeting-webex',
                        'activeTargetController' => $controllerDashboard,
                        'titleLink' => AmosEvents::t('amosevents', 'Webmeeting Webex'),
                        'url' => '/events/event-dashboard/web-meeting-webex?id=' . $model->id
                    ],
                    'dinamicSubMenu' => [
                        'menuWebmeeting1' => [
                            'label' => AmosEvents::t('amosevents', 'Configura'),
                            'url' => '/events/event-dashboard/web-meeting-webex?id=' . $model->id,
                            'activeTargetAction' => 'web-meeting-webex',
                            'activeTargetController' => $controllerDashboard,
                            'titleLink' => AmosEvents::t('amosevents', 'Configura')
                        ],
                        'menuWebmeeting2' => [
                            'label' => AmosEvents::t('amosevents', 'Invitati'),
                            'url' => '/events/event-dashboard/web-meeting-participants?id=' . $model->id,
                            'activeTargetAction' => 'web-meeting-participants',
                            'activeTargetController' => $controllerDashboard,
                            'titleLink' => AmosEvents::t('amosevents', 'Invitati')
                        ],
                    ]
                ];
            }

            if (!empty($model->event_id)) {
                $menu['father'] = [
                    'mainMenu' => [
                        'label' => AmosEvents::t('amosevents', 'Evento Padre'),
                        'icon' => '/sprite/material-sprite.svg#ic_event',
                        'activeTargetAction' => 'event-father',
                        'activeTargetController' => $controllerDashboard,
                        'titleLink' => AmosEvents::t('amosevents', 'Evento Padre'),
                        'url' => '/events/event-dashboard/view?id=' . $model->event_id
                    ],
                ];
            } else if ($hasChildren) {
                $menu['children'] = [
                    'mainMenu' => [
                        'label' => AmosEvents::t('amosevents', 'Eventi figli'),
                        'icon' => '/sprite/material-sprite.svg#ic_event',
                        'activeTargetAction' => 'children',
                        'activeTargetController' => $controllerDashboard,
                        'titleLink' => AmosEvents::t('amosevents', 'Eventi figli'),
                        'url' => '/events/event-dashboard/children?id=' . $model->id
                    ],
                ];
            }

            if (!$isEventInformative || $hasChildren) {
                $menu['community'] =
                    [
                        'mainMenu' => [
                            'label' => AmosEvents::t('amosevents', 'Modifica community'),
                            'icon' => '/sprite/material-sprite.svg#ic_account-supervisor-circle',
                            'activeTargetAction' => 'community',
                            'activeTargetController' => $controllerDashboard,
                            'titleLink' => AmosEvents::t('amosevents', 'Modifica community'),
                            'url' => '/events/event-dashboard/community?id=' . $model->id
                        ],
                    ];

            }

            $menu['email'] = [
                'mainMenu' => [
                    'label' => AmosEvents::t('amosevents', 'Template email'),
                    'icon' => '/sprite/material-sprite.svg#ic_mail',
                    'activeTargetAction' => 'template-emails',
                    'activeTargetController' => $controllerDashboard,
                    'titleLink' => AmosEvents::t('amosevents', 'Template email'),
                    'url' => '/events/event-dashboard/template-emails?id=' . $model->id
                ],
            ];
            $menu['inviti'] = [
                'mainMenu' => [
                    'label' => AmosEvents::t('amosevents', 'Gestione Invii'),
                    'icon' => '/sprite/material-sprite.svg#ic_contact_mail',
                    'activeTargetAction' => 'invite',
                    'activeTargetController' => $controllerDashboard,
                    'titleLink' => AmosEvents::t('amosevents', 'Inviti'),
                    'url' => '/events/event-dashboard/invite?id=' . $model->id
                ],

            ];


            if ($model->status == Event::EVENTS_WORKFLOW_STATUS_PUBLISHED && !$isEventInformative) {
                $menu['partecipanti'] = [
                    'mainMenu' => [
                        'label' => AmosEvents::t('amosevents', 'Gestione partecipanti'),
                        'icon' => '/sprite/material-sprite.svg#ic_settings',
                        'activeTargetAction' => 'info-event',
                        'activeTargetController' => $controllerDashboard,
                        'titleLink' => AmosEvents::t('amosevents', 'Gestione partecipanti'),
                        'url' => '/events/event/view?id=' . $model->id
                    ],
                    'dinamicSubMenu' => [
                        'menuLanding1' => [
                            'label' => AmosEvents::t('amosevents', 'Partecipanti'),
                            'url' => '/events/event/view?id=' . $model->id,
                            'activeTargetAction' => 'view',
                            'activeTargetController' => 'events',
                            'titleLink' => AmosEvents::t('amosevents', 'partecipanti')
                        ],
                        'menuLanding2' => [
                            'label' => AmosEvents::t('amosevents', 'Comunicazioni'),
                            'url' => '/events/event-dashboard/communications?id=' . $model->id,
                            'activeTargetAction' => 'communications',
                            'activeTargetController' => $controllerDashboard,
                            'titleLink' => AmosEvents::t('amosevents', 'Comunicazioni')
                        ],
                    ]
                ];
            } else {
                $menu['comunicazioni'] = [
                    'mainMenu' => [
                        'icon' => '/sprite/material-sprite.svg#ic_settings',
                        'label' => AmosEvents::t('amosevents', 'Comunicazioni'),
                        'url' => '/events/event-dashboard/communications?id=' . $model->id,
                        'activeTargetAction' => 'communications',
                        'activeTargetController' => $controllerDashboard,
                        'titleLink' => AmosEvents::t('amosevents', 'Comunicazioni')
                    ]
                ];
            }
            // SE EVENTO è ANNULLATO
        } else {
            $menu = [
                [
                    'mainMenu' => [
                        'label' => AmosEvents::t('amosevents', 'Dashboard'),
                        'icon' => '/sprite/material-sprite.svg#ic_dashboard',
                        'activeTargetAction' => 'view',
                        'activeTargetController' => $controllerDashboard,
                        'titleLink' => AmosEvents::t('amosevents', 'Evento'),
                        'url' => '/events/event-dashboard/view?id=' . $model->id
                    ],
                ],
            ];
            $menu[] = [
                'mainMenu' => [
                    'label' => AmosEvents::t('amosevents', 'Info evento'),
                    'icon' => '/sprite/material-sprite.svg#ic_event',
                    'activeTargetAction' => 'info-event',
                    'activeTargetController' => $controllerDashboard,
                    'titleLink' => AmosEvents::t('amosevents', 'Info evento'),
                    'url' => '/events/event-dashboard/info-event?id=' . $model->id
                ],
            ];
            $menu[] = [
                'mainMenu' => [
                    'icon' => '/sprite/material-sprite.svg#ic_settings',
                    'label' => AmosEvents::t('amosevents', 'Comunicazioni'),
                    'url' => '/events/event-dashboard/communications?id=' . $model->id,
                    'activeTargetAction' => 'communications',
                    'activeTargetController' => $controllerDashboard,
                    'titleLink' => AmosEvents::t('amosevents', 'Comunicazioni')
                ]
            ];
        }
        return $menu;
    }

    /**
     * @param $model
     * @return array
     */
    public static function getSidebarEventsUser()
    {
        $menu = [

            [
                'mainMenu' => [
                    'label' => AmosEvents::t('amosevents', 'Eventi a cui sono iscritto'),
                    'icon' => '/sprite/material-sprite.svg#ic_calendar-edit',
                    'activeTargetAction' => 'my-events',
                    'titleLink' => AmosEvents::t('amosevents', 'Eventi a cui sono iscritto'),
                    'url' => '/events/event-dashboard/my-events'
                ],
            ],
            [
                'mainMenu' => [
                    'label' => AmosEvents::t('amosevents', 'Eventi con biglietto'),
                    'icon' => '/sprite/material-sprite.svg#ic_ticket-account',
                    'activeTargetAction' => 'my-tickets',
                    'titleLink' => AmosEvents::t('amosevents', 'I miei eventi con biglietto'),
                    'url' => '/events/event-dashboard/my-tickets'
                ],
            ],
            [
                'mainMenu' => [
                    'label' => AmosEvents::t('amosevents', 'Eventi a cui sono stato invitato'),
                    'icon' => '/sprite/material-sprite.svg#ic_event',
                    'activeTargetAction' => 'my-invitations',
                    'titleLink' => AmosEvents::t('amosevents', 'Eventi a cui sono stato invitato'),
                    'url' => '/events/event-dashboard/my-invitations'
                ],
            ],
            [
                'mainMenu' => [
                    'label' => AmosEvents::t('amosevents', 'Eventi di mio interesse'),
                    'icon' => '/sprite/material-sprite.svg#ic_calendar-heart',
                    'activeTargetAction' => 'own-interest',
                    'titleLink' => AmosEvents::t('amosevents', 'Eventi di mio interesse'),
                    'url' => '/events/event-dashboard/own-interest'
                ],
            ],


        ];

        return $menu;
    }

    /**
     * @param $model
     * @return array
     */
    public static function getSidebarManageEvents()
    {
        $menu = [

            [
                'mainMenu' => [
                    'label' => AmosEvents::t('amosevents', 'Gestisci tutti gli eventi'),
                    'icon' => '/sprite/material-sprite.svg#ic_calendar-edit',
                    'activeTargetAction' => 'manage-all-events',
                    'titleLink' => AmosEvents::t('amosevents', 'Gestisci tutti gli eventi'),
                    'url' => '/events/event-dashboard/manage-all-events'
                ],
            ],
            [
                'mainMenu' => [
                    'label' => AmosEvents::t('amosevents', 'Eventi in evidenza'),
                    'icon' => '/sprite/material-sprite.svg#ic_event',
                    'activeTargetAction' => 'index-highlights',
                    'titleLink' => AmosEvents::t('amosevents', 'Eventi in evidenza'),
                    'url' => '/events/event-dashboard/index-highlights'
                ],
            ],


        ];

        return $menu;
    }

    /**
     * @param $nav_id
     */
    public static function getTemplatePreviewHtml($nav_id)
    {
        $utility = new \amos\cmsbridge\utility\CmsUtility();
        $page = new \amos\cmsbridge\models\PostCmsCreatePage();

        $page->nav_id = $nav_id;
        $page->cms_user_id = $utility->loginCms();
        $page->lang_id = 1; // lingua
        $result = $utility->getPreviewUrlCmsPageHtml($page);
        return $result;
        //        pr($result);

    }


    /**
     * @param $id
     */
    public static function setScope($id)
    {
        $moduleCwh = \Yii::$app->getModule('cwh');
        if (isset($moduleCwh)) {
            $moduleCwh->setCwhScopeInSession(
                [
                    'community' => $id,
                ],
                [
                    'mm_name' => 'community_user_mm',
                    'entity_id_field' => 'community_id',
                    'entity_id' => $id
                ]
            );
        }
    }

    public static function doSendInvitations($eid, array $rows, $registerSendDatetime = false, $saveInvitationSent = false)
    {
        $cnt = 0;
        $errs = '';
        $controller = \Yii::$app->controller;

        /** @var Event $eventModel */
        $eventModel = $controller->eventsModule->createModel('Event');
        $event = $eventModel::findOne(['id' => $eid]);

        foreach ($rows as $r => $row) {
            try {
                // Sets sender
                $from = $controller->getFromMail($event);
                EventMailUtility::setLayoutMail($event->email_ticket_layout_custom);
                if ($controller->eventsModule->enableAutoInviteUsers && ($row['type'] == EventInvitation::INVITATION_TYPE_REGISTERED)) {
                    $user = User::findOne($row['user_id']);
                    $profile = $user->userProfile;
                    // Build url signup with user's data
                    $extUrlYes = Url::base(true) . Url::toRoute([
                            '/events/event/event-signup', 'eid' => $event->id, 'pName' => $row['name'],
                            'pSurname' => $row['surname'], 'pEmail' => $row['email'], 'pCode' => $row['code']
                        ]);
                    $regUrlNo = Url::base(true) . Url::toRoute(['reject', 'id' => $event->id]);
                    $row['email'] = $user['email'];
                    $viewInvitation = 'email_invitation_registered';
                    if (!empty($event->email_invitation_custom)) {
                        $viewInvitation = $event->email_invitation_custom;
                    }
                    $text = $controller->renderPartial(
                        $viewInvitation,
                        [
                            'event' => $event,
                            'user' => $user,
                            'profile' => $profile,
                            'urlYes' => $extUrlYes,
                            'urlNo' => $regUrlNo,
                        ]
                    );
                } else {
                    $viewInvitation = 'email_invitation';
                    if (!empty($event->email_invitation_custom)) {
                        $viewInvitation = $event->email_invitation_custom;
                    }
                    $extUrlYes = Url::base(true) . Url::toRoute([
                            '/events/event/event-signup', 'eid' => $event->id, 'pName' => $row['name'],
                            'pSurname' => $row['surname'], 'pEmail' => $row['email']
                        ]);
                    $text = $controller->renderPartial(
                        $viewInvitation,
                        [
                            'event' => $event,
                            'user' => $row,
                            'urlYes' => $extUrlYes
                        ]
                    );
                }
                // Sends e-mail
                $ok = Email::sendMail(
                    $from,
                    [$row['email']],
                    'Invito - ' . html_entity_decode($event->title),
                    $text,
                    [],
                    [],
                    [],
                    0,
                    false
                );
                if ($saveInvitationSent && $ok) {
                    $intivationSent = new EventInvitationSent();
                    $intivationSent->event_internal_list_id = $row['event_internal_list_id'];
                    $intivationSent->user_id = $row['user_id'];
                    $intivationSent->email = $row['email'];
                    $intivationSent->send_at = date('Y-m-d H:i:s');
                    $intivationSent->template = $row['template'];
                    $intivationSent->save();
                }
                if ($registerSendDatetime && $ok) {
                    // Marks invitation as sent
                    /** @var EventInvitation $eventInvitationModel */
                    $eventInvitationModel = $controller->eventsModule->createModel('EventInvitation');
                    $invitation = $eventInvitationModel::findOne($row['id']);
                    $invitation->invitation_sent_on = new \yii\db\Expression('now()');
                    $invitation->save();
                }

                ++$cnt;
            } catch (\Exception $e) {
                $errs .= (strlen($errs) > 0 ? '<br>' : '') . $e->getMessage();
                $errs .= '<br>' . 'Errore in fase di importazione della riga ' . (1 + $r) . ' : codice fiscale già presente ';
            }
        }

        return ['cnt' => $cnt, 'errs' => $errs];
    }

    /**
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public static function getCurrentDg($operator = true)
    {
        $query = EventGroupReferent::find()
            ->innerJoinWith('eventGroupReferentMms')
            ->andWhere(['user_id' => \Yii::$app->user->id]);
        if ($operator) {
            $query->andWhere(['exclude_from_query' => true]);
        }
        $group = $query->one();
        return $group;
    }

    public static function getCurrentDgId($operator = true)
    {
        $query = EventGroupReferent::find()
            ->innerJoinWith('eventGroupReferentMms')
            ->andWhere(['user_id' => \Yii::$app->user->id]);
        if ($operator) {
            $query->andWhere(['exclude_from_query' => true]);
        }
        $group = $query->limit(1)->select('event_group_referent.id')->scalar();
        return $group;
    }


    /**
     * @param $user_id
     * @param $model Event|Community
     * @return bool
     * @throws \yii\base\InvalidConfigException
     */
    public static function isUserEventOperator($user_id, $model)
    {
        if (\Yii::$app->user->can('SUPER_USER_EVENT')) {
            return true;
        }
        $group = self::getCurrentDg();
        $groupReferentMm = null;
        if ($group) {
            if (get_class($model) == Community::className()) {
                $event = Event::find()
                    ->andWhere(['community_id' => $model->id])->one();
            } else {
                $event = $model;
            }
            $groupReferentMm = EventGroupReferentMm::find()
                ->innerJoin('event', 'event.event_group_referent_id = event_group_referent_mm.event_group_referent_id')
                ->andWhere(['event.id' => $event->id])
                ->andWhere(['exclude_from_query' => 1])
                ->andWhere(['event_group_referent_mm.event_group_referent_id' => $group->id])
                ->andWhere(['event_group_referent_mm.user_id' => $user_id])->one();
        }
        if (!empty($groupReferentMm)) {
            if (\Yii::$app->authManager->checkAccess($user_id, 'EVENT_DG_OPERATOR')) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param $user_id
     * @param $model Event|Community
     * @return bool
     * @throws \yii\base\InvalidConfigException
     */
    public static function isUserOperatorAdministratorDg()
    {
        $module = \Yii::$app->getModule('events');
        $administratorDgId = $module->groupReferentAdministration['id'];
        $currentDgId = \open20\amos\events\utility\EventsUtility::getCurrentDgId();
        if ($currentDgId == $administratorDgId) {
            return true;
        }
        return false;
    }

    /**
     * @param $model Event
     * @return string
     */
    public static function getUrlLanding($model)
    {
        $landing = $model->eventLanding;
        if ($landing) {
            $url = \Yii::$app->params['platform']['frontendUrl'] . '/it/' . $landing->url;
            if ($model->multilanguage) {
                if (\Yii::$app->language == 'en-GB') {
                    $url .= '?language=en-GB';
                }
            }

            return $url;
        }
        return '';
    }

    /**
     * @param $model Event
     * @return string
     */
    public static function getUrlLandingPreview($model)
    {
        $landing = $model->eventLanding;
        if ($landing) {
            return \Yii::$app->params['platform']['frontendUrl'] . '/api/1/preview-event?itemId=' . $landing->luya_page_id;
        }
        return '';
    }

    /**
     * @param $title
     * @return string
     */
    public static function getClassPreviewLanding($title)
    {
        return 'gradient-' . str_replace('/', '-', strtolower($title));
    }

    /**
     * @param $event
     * @return bool
     * @throws \yii\base\InvalidConfigException
     */
    public static function canViewCommunityEvent($event)
    {
        if ($event->community_id && $event->show_community) {
            $ismemeber = \open20\amos\community\models\CommunityUserMm::find()
                ->andWhere(['community_id' => $event->community_id, 'user_id' => Yii::$app->user->id])->count();
            return $ismemeber > 0;
        }
        return false;
    }

    /**
     * @param $event
     * @return bool
     */
    public static function isEventCommunityEnabled($event)
    {
        if ($event->getEventCommunityId() && $event->show_community) {
            return true;
        }
        return false;
    }


    /**
     * @return array
     */
    public static function getLabelPreference()
    {

        $tags = EventsUtility::getPreferenceCenterTags();
        $preferenceTags = [];
        $assetBundle = WizardEventAsset::register(\Yii::$app->controller->view);
        $baseUrl = $assetBundle->baseUrl;

        foreach ($tags as $tag) {
//            $tag->codice;
//            $src = $baseUrl . '/img/' . $tag->codice . '.png';
            // $preferenceTags [$tag->id] = Html::img($src, ['class' => 'img-tag']). $tag->nome;
//            $preferenceTags [$tag->id] = "<div class='img-tag'>" . Html::img($src, ['class' => '']) . "</div>" . $tag->nome;
            $preferenceTags [$tag->id] = "<div class='img-tag'>" . "</div>" . $tag->nome;
        }
        return $preferenceTags;
    }

    /**
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public function currentDgEvents()
    {
        $group = self::getCurrentDg();
        if ($group) {
            return Event::find()->andWhere(['event_group_referent_id' => $group->id])->all();
        }
        if (\Yii::$app->user->can('SUPER_USER_EVENT')) {
            return Event::find()->all();
        }
        return [];
    }


    /**
     * @param $currentDgId
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public static function eventFathersQuery($currentDgId)
    {
        $moduleEvents = AmosEvents::instance();
        $administrationDg = EventGroupReferent::findOne(['id' => $moduleEvents->groupReferentAdministration['id']]);
        $query = \open20\amos\events\models\Event::find()->andWhere(['is_father' => true]);

        if ($administrationDg && $administrationDg->id != $currentDgId) {
            $query->andWhere(['event_group_referent_id' => $currentDgId]);
        }
        return $query;
    }

    /**
     * @return string
     */
    public static function getToolbarTextEditor()
    {
        //  "fullscreen | undo redo code | styleselect | bold italic strikethrough forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media insertdatetime | removeformat | keyvalues",
        return "fullscreen | undo redo | bold italic underline strikethrough | bullist numlist | link | keyvalues";
    }

    /**
     * @param $courseId
     * @param $user_id
     * @return bool
     */
    public static function checkIfCourseIsCompleted($courseId, $user_id)
    {
//        $courseId = 9;
        $completed_statuses = \open20\amos\moodle\models\ServiceCall::ACTIVITY_STATUS_COMPLETED_LIST;
        $serviceCall = new \open20\amos\moodle\models\ServiceCall();
        $topicId = null;

        $moodleUser = \open20\amos\moodle\models\MoodleUser::find()->andWhere(['user_id' => $user_id])->one();
        $whitelist = ['scorm'];
        $results = [];
        if (!empty($moodleUser) && !empty($courseId)) {
            //   pr($moodleUser->moodle_email);
            //   pr($content, $moodleUser->moodle_email);
            try {
                $contentsList = $serviceCall->getActivitiesCompletionStatus($courseId, $moodleUser->moodle_userid);
//                $contentsList = array_reverse($contentsList);
                $content = array_pop($contentsList);

//                pr($content, 'content');
                if (in_array($content['modname'], $whitelist)) {
                    if (in_array($content['state'], $completed_statuses)) {
                        return true;
                    }
                }

                return false;
//                foreach ($contentsList as $key => $content) {
//
//                    if (!isset($results[$content['cmid']])) {
//                        $results[$content['cmid']] = false;
//                    }
//                    if (in_array($content['modname'], $whitelist)) {
//                        if (in_array($content['state'], $completed_statuses)) {
//                            $results[$content['cmid']] = true;
//                        }
//                    }
//                }
//                // check if all courses are completed
//                if (count($results) == 0) {
//                    return false;
//                }
//                foreach ($results as $result) {
//                    if ($result == false) {
//                        return false;
//                    }
//                }
//                return true;
            } catch (\open20\amos\moodle\exceptions\MoodleException $e) {

            }


        }
        return false;
    }

    /**
     * @param $model
     */
    public static function createUpdateCommunity($model)
    {
        $isCreate = false;
        if (is_null($model->community_id)) {
            $managerStatus = CommunityUserMm::STATUS_ACTIVE;//$this->getManagerStatus($model, $oldAttributes);
            $ok = EventsUtility::createCommunity($model, $managerStatus, CommunityType::COMMUNITY_TYPE_CLOSED);
            if (!empty($model->show_community)) {
                $model->show_community = true;
            } else {
                $model->show_community = false;
            }
            $model->save(false);
            $isCreate = true;
        } else {
            $ok = EventsUtility::updateCommunity($model);
        }
    }


    /**
     * @param $event
     * @param $date
     * @param $dateend
     * @param bool $enableTimePeriod
     * @return bool
     */
    public static function copyEvent($event, $date, $dateend, $enableTimePeriod = false)
    {
        $moduleAttachments = \Yii::$app->getModule('attachments');
        $copyEvent = new Event();
        $commmunity = Community::findOne($event->community_id);


        $oldBeginDate = new \DateTime($event->begin_date_hour);
        $oldEndDate = new \DateTime($event->end_date_hour);
        $beginDate = new \DateTime($date);

        // if $dateend is null calculate the date end
        if (!empty($dateend)) {
            $endDate = new \DateTime($dateend);
        } else {
            $diff = $oldBeginDate->diff($oldEndDate);
            $endDate = clone $beginDate;
            $endDate = $endDate->add($diff);
        }
//        pr($diff,'diff');

        $params ['Event'] = $event->attributes;
        $copyEvent->load($params);
        $copyEvent->community_id = null;
        $copyEvent->webmeeting_webex_id = null;
        $copyEvent->begin_date_hour = $date;
        $copyEvent->end_date_hour = $endDate->format('Y-m-d H:i:s');
        $copyEvent->original_event_id = $event->id;
        $copyEvent->is_time_period = $enableTimePeriod;
        $copyEvent->status = Event::EVENTS_WORKFLOW_STATUS_DRAFT;
//        pr($copyEvent->attributes);


        // Location
        $copyEventLocation = new EventLocation();
        if ($event->eventLocation->hidden == 1) {
            $paramsLoc['EventLocation'] = $event->eventLocation->attributes;
            $copyEventLocation->load($paramsLoc);
            $copyEventLocation->save(false);
            $copyEvent->event_location_id = $copyEventLocation->id;

            // Entrance
            $copyEventLocationEntr = new EventLocationEntrances();
            $paramsLoc['EventLocationEntrances'] = $event->eventLocationEntrance->attributes;
            $copyEventLocationEntr->load($paramsLoc);
            $copyEventLocationEntr->event_location_id = $copyEventLocation->id;
            $copyEventLocationEntr->save(false);
            $copyEvent->event_location_entrance_id = $copyEventLocationEntr->id;
        }
        // EVENT CWH PUBLICATION
        $copyEvent->destinatari = $event->destinatari;
        $copyEvent->validatori = $event->validatori;
        $copyEvent->regola_pubblicazione = \open20\amos\cwh\models\CwhRegolePubblicazione::ALL_USERS;


        // SAVE EVENT
        $copyEvent->callLyua = false;
        $copyEvent->save(false);

        // SAVE LOGO event
        if ($moduleAttachments) {
            if ($copyEvent->eventLogo) {
                $copyEvent->eventLogo->delete();
            }
            /** @var  $logo File */
            $logo = $event->eventLogo;
            if ($logo) {
                $moduleAttachments->attachFile($logo->getPath(), $copyEvent, 'eventLogo', false);
            }

            /** @var  $logo File */
            if ($copyEvent->eventLogoMobile) {
                $copyEvent->eventLogoMobile->delete();
            }
            /** @var  $logo File */
            $logoMobile = $event->eventLogoMobile;
            if ($logoMobile) {
                $moduleAttachments->attachFile($logoMobile->getPath(), $copyEvent, 'eventLogoMobile', false);
            }

        }

        //COPY COMMUNITY
        self::createUpdateCommunity($copyEvent);
        $copyCommunity = $copyEvent->community;
        $copyCommunity->force_workflow = $commmunity->force_workflow;
        $copyCommunity->save(false);
        $copyEvent->show_community = $event->show_community;

        // COPY WIDGET DASHBOARD COMMUNITY
        $widgetsCommunity = CommunityAmosWidgetsMm::find()->andWhere(['community_id' => $commmunity->id])->all();
        foreach ($widgetsCommunity as $widget) {
            $copyWidget = new CommunityAmosWidgetsMm();
            $copyWidget->community_id = $copyCommunity->id;
            $copyWidget->amos_widgets_id = $widget->amos_widgets_id;
            $copyWidget->personalized = $widget->personalized;
            $copyWidget->widget_label = $widget->widget_label;
            $copyWidget->save(false);
        }


        // COPY TAGS
        $event->loadTagPreferences();
        $event->loadCustomTags();
        $copyEvent->preferencesTags = $event->preferencesTags;
        $copyEvent->customTags = $event->customTags;
        $copyEvent->customTagsDefault = $event->customTagsDefault;
        $copyEvent->savePreferencesTags();
        $copyEvent->saveCustomTags();

        // EXTRA
        $copyEvent->createDefaultAccreditationList();


        // EVENT EMAIL TEMPLATE
        $copyEventEmailTemplate = new EventEmailTemplates();
        $paramsEmail['EventEmailTemplates'] = $event->eventEmailTemplates->attributes;
        $copyEventEmailTemplate->load($paramsEmail);
        $copyEventEmailTemplate->event_id = $copyEvent->id;
        $copyEventEmailTemplate->save(false);

        //EVENT SEATS
        $seats = $event->eventSeats;
        foreach ($seats as $seat) {
            $paramsSeat ['EventSeats'] = $seat->attributes;
            $copySeats = new EventSeats();
            $copySeats->load($paramsSeat);
            $copySeats->user_id = null;
            $copySeats->event_participant_companion_id = null;
            $copySeats->event_id = $copyEvent->id;
            $copySeats->save(false);
        }

        // EVENT INTERNAL LIST
        if (!$event->eventType->webmeeting_webex) {
            $internalLists = $event->eventInternalLists;
            foreach ($internalLists as $list) {
                $copyInternalList = new EventInternalList();
                $paramsIntList['EventInternalList'] = $list->attributes;
                $copyInternalList->load($paramsIntList);
                $copyInternalList->event_id = $copyEvent->id;
                $copyInternalList->status = null;
                $copyInternalList->save(false);
            }
        }

        // EVENT COMMUNICATION
        $communications = $event->eventCommunications;
        foreach ($communications as $comunication) {
            $copyCommunication = new EventCommunication();
            $paramsCom['EventCommunication'] = $comunication->attributes;
            $copyCommunication->load($paramsCom);
            $copyCommunication->event_id = $copyEvent->id;
            $copyCommunication->status = null;
            $copyCommunication->save(false);

        }


        // LANDING
        $copyEventLanding = new EventLanding();
        $paramsLanding['EventLanding'] = $event->eventLanding->attributes;
        $copyEventLanding->load($paramsLanding);
//        pr($paramsLanding);
        $copyEventLanding->url = $event->eventLanding->url . '-' . $copyEvent->id;
        $copyEventLanding->event_id = $copyEvent->id;
        $copyEventLanding->luya_page_id = null;
        $ok = $copyEventLanding->save(false);


        // NEWS LANDING
        $landingNews = $event->eventLanding->eventLandingNews;
        foreach ($landingNews as $Lnews) {
            $copyLandingNews = new EventLandingNews();
            $paramsLandingNews['EventLandingNews'] = $Lnews->attributes;
            $copyLandingNews->load($paramsLandingNews);
            $copyLandingNews->event_landing_id = $copyEventLanding->id;
            $copyLandingNews->save(false);
        }


        // PROTAGONIST
        $protagonists = $event->eventLanding->eventLandingProtagonists;
        foreach ($protagonists as $protagonist) {
            $paramsProt ['EventLandingProtagonist'] = $protagonist->attributes;
            $copyProtagonist = new EventLandingProtagonist();
            $copyProtagonist->load($paramsProt);
            $copyProtagonist->event_landing_id = $copyEventLanding->id;
            $copyProtagonist->save(false);

            //save image attachment
            if ($copyProtagonist->image) {
                $copyProtagonist->image->delete();
            }
            $fileImage = $protagonist->image;
            if ($fileImage) {
                $moduleAttachments->attachFile($fileImage->getPath(), $copyProtagonist, 'image', false);
            }
        }

        // EVENTI CORRELATI
        $relatedEvents = $event->eventRelateds;
        foreach ($relatedEvents as $relatedEvent) {
            $paramsRelate ['EventRelated'] = $relatedEvent->attributes;
            $copyRelatedEvent = new EventRelated();
            $copyRelatedEvent->load($paramsRelate);
            $copyRelatedEvent->event_id = $copyEvent->id;
            $copyRelatedEvent->save(false);
        }

        // INSTAGRAM VIDEO
        $sliderVideoIg = $event->eventLanding->instagramVideoSlider;
        if (!empty($sliderVideoIg)) {
            $videosIg = $sliderVideoIg->getSliderElems()->all();
            $cloneSliderIg = new \amos\sitemanagement\models\SiteManagementSlider();
            $paramsSliderIg['SiteManagementSlider'] = $sliderVideoIg->attributes;
            $cloneSliderIg->load($paramsSliderIg);
            $cloneSliderIg->save(false);
            $copyEventLanding->instagram_video_slider_id = $cloneSliderIg->id;
            $copyEventLanding->save(false);

            foreach ($videosIg as $video) {
                $copyVideo = new \amos\sitemanagement\models\SiteManagementSliderElem();
                $paramsVideo ['SiteManagementSliderElem'] = $video->attributes;
                $copyVideo->load($paramsVideo);
                $copyVideo->slider_id = $cloneSliderIg->id;
                $copyVideo->save(false);
            }
        }

        // VIDEO
        $sliderVideo = $event->eventLanding->videoSlider;
        if (!empty($sliderVideo)) {
            $videos = $sliderVideo->getSliderElems()->all();
            $cloneSlider = new \amos\sitemanagement\models\SiteManagementSlider();
            $paramsSlider['SiteManagementSlider'] = $sliderVideo->attributes;
            $cloneSlider->load($paramsSlider);
            $cloneSlider->save(false);
            $copyEventLanding->video_slider_id = $cloneSlider->id;
            $copyEventLanding->save(false);

            foreach ($videos as $video) {
                $copyVideo = new \amos\sitemanagement\models\SiteManagementSliderElem();
                $paramsVideo ['SiteManagementSliderElem'] = $video->attributes;
                $copyVideo->load($paramsVideo);
                $copyVideo->slider_id = $cloneSlider->id;
                $copyVideo->save(false);
            }

        }

        // PICS
        $sliderImage = $event->eventLanding->imageSlider;
        if (!empty($sliderImage)) {
            $images = $sliderImage->getSliderElems()->all();
            $cloneSliderImg = new \amos\sitemanagement\models\SiteManagementSlider();
            $paramsSlider['SiteManagementSlider'] = $sliderImage->attributes;
            $cloneSliderImg->load($paramsSlider);
            $cloneSliderImg->save(false);
            $copyEventLanding->image_slider_id = $cloneSliderImg->id;
            $copyEventLanding->save(false);


            foreach ($images as $image) {
                $copyImage = new \amos\sitemanagement\models\SiteManagementSliderElem();
                $paramsImage ['SiteManagementSliderElem'] = $image->attributes;
                $copyImage->load($paramsImage);
                $copyImage->slider_id = $cloneSliderImg->id;
                $copyImage->save(false);

                /** @var  $logo File */
                if ($copyImage->fileImage) {
                    $copyImage->fileImage->delete();
                }
                $fileImage = $image->fileImage;

                if ($fileImage) {
                    $moduleAttachments->attachFile($fileImage->getPath(), $copyImage, 'fileImage', false);
                }
            }
        }

        // SEO
        /** @var  $logo File */
        $fileImageOg = $event->eventLanding->ogImageFile;
        if ($fileImageOg) {
            $moduleAttachments->attachFile($fileImageOg->getPath(), $copyEventLanding, 'ogImageFile', false);
        }
        $seoData = SeoData::find()->andWhere(['classname' => Event::className(), 'content_id' => $event->id])->one();
        $copySeoData = SeoData::find()->andWhere(['classname' => Event::className(), 'content_id' => $copyEvent->id])->one();
        if ($seoData) {
            if (empty($copySeoData)) {
                $copySeoData = new SeoData();
            }
            $copySeoData->classname = Event::className();
            $copySeoData->content_id = $copyEvent->id;
            $copySeoData->meta_title = $seoData->meta_title;
            $copySeoData->og_description = $seoData->og_description;
            $copySeoData->save(false);
        }


        self::copyContentsCommunity($commmunity, $copyCommunity, $event, $copyEventLanding);
        $copyEventLanding->createLandingPage($copyEvent);
//        pr($copyEventLanding->attributes);
//        DIE;
        return true;
    }

    /**
     * @param $community
     * @param $newCommunity
     * @param $event
     * @param $newEventLanding
     * @return void
     * @throws \yii\base\InvalidConfigException
     */
    public static function copyContentsCommunity($community, $newCommunity, $event, $newEventLanding)
    {
        if ($community) {
            $moduleAttachments = \Yii::$app->getModule('attachments');
            self::setScope($community->id);

            //NEWS
            $modelSearch = new \open20\amos\news\models\search\NewsSearch();
            $dataProviderNews = $modelSearch->searchAll([]);
            $dataProviderNews->pagination = false;
            $newsList = [];
            foreach ($dataProviderNews->models as $news) {
                $copyNews = new \open20\amos\news\models\News();
                $paramsNews ['News'] = $news->attributes;
                $copyNews->load($paramsNews);
                $copyNews->destinatari = ['community-' . $newCommunity->id];
                $copyNews->validatori = ['community-' . $newCommunity->id];
                $copyNews->regola_pubblicazione = 3;
                $copyNews->detachBehavior('workflow');
                $copyNews->save(false);

                $fileImage = $news->newsImage;
                if ($fileImage) {
                    $moduleAttachments->attachFile($fileImage->getPath(), $copyNews, 'newsImage', false);
                }

                // faccio associazione ed evento
                $copyLandingNews = new EventLandingNews();
                $copyLandingNews->event_landing_id = $newEventLanding->id;
                $copyLandingNews->news_id = $copyNews->id;
                $copyLandingNews->save(false);
            };


            // DISCUSSIONI
            $modelSearch = new \open20\amos\discussioni\models\search\DiscussioniTopicSearch();
            $dataProviderDisc = $modelSearch->searchAll([]);
            $dataProviderDisc->pagination = false;
            foreach ($dataProviderDisc->models as $discussione) {
                $copyDisc = new \open20\amos\discussioni\models\DiscussioniTopic();
                $paramsDisc ['DiscussioniTopic'] = $discussione->attributes;
                $copyDisc->load($paramsDisc);
                $copyDisc->destinatari = ['community-' . $newCommunity->id];
                $copyDisc->validatori = ['community-' . $newCommunity->id];
                $copyDisc->regola_pubblicazione = 3;
                $copyDisc->detachBehavior('workflow');
                $copyDisc->save(false);

                $fileImage = $discussione->discussionsTopicImage;
                if ($fileImage) {
                    $moduleAttachments->attachFile($fileImage->getPath(), $copyDisc, 'discussionsTopicImage', false);
                }

            };


            // DOCUMENTI
            $modelSearch = new \open20\amos\documenti\models\search\DocumentiSearch();
            $dataProviderDoc = $modelSearch->searchAll([]);
            $dataProviderDoc->pagination = false;
            foreach ($dataProviderDoc->models as $documento) {
                $copyDoc = new \open20\amos\documenti\models\Documenti();
                $paramsDoc['Documenti'] = $documento->attributes;
                $copyDoc->load($paramsDoc);
                $copyDoc->destinatari = ['community-' . $newCommunity->id];
                $copyDoc->validatori = ['community-' . $newCommunity->id];
                $copyDoc->regola_pubblicazione = 3;
                $copyDoc->detachBehavior('workflow');
                $copyDoc->save(false);

                $fileImage = $documento->documentMainFile;
                if ($fileImage) {
                    $moduleAttachments->attachFile($fileImage->getPath(), $copyDoc, 'documentMainFile', false);
                }

                // faccio associazione ed evento
                $copyLandingDoc = new EventLandingDocuments();
                $copyLandingDoc->event_landing_id = $newEventLanding->id;
                $copyLandingDoc->documenti_id = $copyDoc->id;
                $copyLandingDoc->save(false);

            };


            self::resetScope();

        }
    }

    /**
     *
     */
    public static function resetScope()
    {
        $cwhModule = \Yii::$app->getModule('cwh');
        if (isset($cwhModule)) {
            /** @var \open20\amos\cwh\AmosCwh $cwhModule */
            $cwhModule->resetCwhScopeInSession();
        }
    }


    /**
     * @param $model
     * @param string $baseUrl
     * @param string $class
     * @return string
     */
    public static function getButtonCommunity($model, $baseUrl = '', $class = 'btn btn-secondary btn-xs mx-1')
    {
        //if is an operator subscribe as as staff directly
        if (\open20\amos\events\utility\EventsUtility::isEventCommunityEnabled($model) && $model->status != \open20\amos\events\models\Event::EVENTS_WORKFLOW_STATUS_SUSPENDED) {
            $dg = \open20\amos\events\utility\EventsUtility::getCurrentDgId();
            if (\Yii::$app->user->can('SUPER_USER_EVENT') || (\Yii::$app->user->can('EVENT_DG_OPERATOR') && $dg && $model->event_group_referent_id == $dg)) {
                $modelId = $model->id;
                if ($model->is_time_period) {
                    $modelId = $model->event_id;
                }
                $url = $baseUrl . '/events/event-dashboard/join-community?id=' . $modelId;
                $canViewCommunity = true;
            } else {
                $url = $baseUrl . '/events/event-dashboard/join?id=' . $model->id;
                $canViewCommunity = \open20\amos\events\utility\EventsUtility::canViewCommunityEvent($model);
            }
            if ($model->multilanguage) {
                if (\Yii::$app->language == 'en-GB') {
                    $url .= '&language=en-GB';
                }
            }
            if ($canViewCommunity) {
                return Html::a('Community', $url, ['class' => $class, 'title' => AmosEvents::t('amosevents', "Vai alla Community")]);
            }
        }
        return '';
    }

    /**
     * @param $model
     * @param string $baseUrl
     * @param string $class
     * @param bool $iconEnabled
     * @return string
     */
    public static function getButtonDownloadTicket($model, $baseUrl = '', $class = 'btn btn-primary btn-lg', $iconEnabled = false)
    {
        $btn = '';
        $icon = "<span class='mdi mdi-ticket-account rounded-icon rounded-secondary text-white'></span>";
        $invitation = EventInvitation::findOne(['user_id' => \Yii::$app->user->id, 'event_id' => $model->id]);
        if ($invitation && !empty($invitation)) {
            if ($model->seats_management) {
                if (!$invitation->is_ticket_sent) {
                    return '';
                }
            }
            $btn = (($model->has_tickets && $invitation->everyoneInSameInvitationHasAccreditationList())
                ? Html::a(
                    $iconEnabled ? $icon : AmosEvents::txt('Scarica biglietto'),
                    $baseUrl . '/events/event/download-tickets?eid=' . $model->id . '&iid=' . $invitation->id . '&code=' . $invitation->code,
                    [
                        'class' => $class,
                        'title' => AmosEvents::t('amosevents', "Scarica biglietto"),
                        'target' => '_blank',
                        'data-toggle' => 'tooltip'
                    ]
                )
                : '');
        }
        return $btn;
    }


    /**
     * @param $webmeeting
     * @param $model
     */
    public static function checkDatesWebmeeting($webmeeting, $model, $stringError = false)
    {
        $beginDateEvent = new \DateTime($model->begin_date_hour);
        $endDateEvent = new \DateTime($model->end_date_hour);
        $startDate = new \DateTime($webmeeting->start);
        $endDate = new \DateTime($webmeeting->end);
        $registerEndDate = $model->registration_date_end ? new \DateTime($model->registration_date_end) : null;
        $now = new \DateTime();

        $stringErrors = [];
        if ($startDate > $endDate) {
            $text = AmosEvents::t('amosevents', 'La data di fine del meeting deve essere successiva alla data/ora di fine del meeting');
            $webmeeting->addError('end_date', $text);
            $webmeeting->addError('end_hour', $text);
            if ($stringError) {
                $stringErrors [] = $text;
            } else {
                return false;
            }
        }

        if ($startDate < $now) {
            $text = AmosEvents::t('amosevents', 'La data di inizio del meeting deve essere maggiore della data/ora attuale');

            $webmeeting->addError('start_date', $text);
            $webmeeting->addError('start_hour', $text);
            if ($stringError) {
                $stringErrors [] = $text;
            } else {
                return false;
            }
        }

        if ($startDate < $beginDateEvent) {
            $text = AmosEvents::t('amosevents', 'La data di inizio del meeting deve essere maggiore della data/ora inizio evento');

            $webmeeting->addError('start_date', $text);
            $webmeeting->addError('start_hour', $text);
            if ($stringError) {
                $stringErrors [] = $text;
            } else {
                return false;
            }
        }

        if ($endDate <= $beginDateEvent) {
            $text = AmosEvents::t('amosevents', 'La data di fine del meeting deve essere successiva della data/ora inizio evento');

            $webmeeting->addError('end_date', $text);
            $webmeeting->addError('end_hour', $text);
            if ($stringError) {
                $stringErrors [] = $text;
            } else {
                return false;
            }
        }

        if ($endDate > $endDateEvent) {
            $text = AmosEvents::t('amosevents', 'La data di fine del meeting deve essere inferiore alla data/ora di fine evento');

            $webmeeting->addError('end_date', $text);
            $webmeeting->addError('end_hour', $text);
            if ($stringError) {
                $stringErrors [] = $text;
            } else {
                return false;
            }
        }

        $durataMeeting = abs($endDate->getTimestamp() - $startDate->getTimestamp()) / 3600;
        if ($durataMeeting > 24) {
            $text = AmosEvents::t('amosevents', 'La durata del meeting deve essere inferiore alle 24 ore');

            $webmeeting->addError('end_date', $text);
            $webmeeting->addError('end_hour', $text);
            if ($stringError) {
                $stringErrors [] = $text;
            } else {
                return false;
            }
        }

        if ($stringError) {
            return $stringErrors;
        }

        return true;
    }

    /**
     * @param $webmeeting
     * @param null $model Event
     * @return null|string
     */
    public static function errorsUpdateWebmeeting($webmeeting, $model = null)
    {
        $startDate = new \DateTime($webmeeting->start);
        $endDate = new \DateTime($webmeeting->end);
        $now = new \DateTime();
        $textError = null;

        if ($now > $startDate) {
            $textError = AmosEvents::t('amosevents', "Il meeting è attualmente in corso. Non è possibile apportare modifiche");
        }
        if ($now > $endDate) {
            $textError = AmosEvents::t('amosevents', "Il meeting è scaduto. Non è possibile apportare modifiche");
        }

        if (!empty($model) && $model->status != Event::EVENTS_WORKFLOW_STATUS_PUBLISHED) {
            $textError = '';
        }

        return $textError;
    }

    /**
     * @param $model Event
     * @return bool
     * @throws \Exception
     */
    public static function canModifyWebexParticipant($model)
    {
        if ($model->status != Event::EVENTS_WORKFLOW_STATUS_PUBLISHED) {
            return true;
        }
        if ($model->webMeetingWebex) {
            $now = new \DateTime();
            $interval = new \DateInterval('PT1H');
            $startMeeting = new \DateTime($model->webMeetingWebex->start);
            $startMeeting->sub($interval);
            if ($now <= $startMeeting) {
                return true;
            }
            return false;
        }
        return false;

    }


    /**
     * @param $apiResponse
     * @param $event_id
     */
    public static function saveUserInvitedFromWebex($apiResponse, $event_id)
    {
        foreach ($apiResponse['items'] as $invitee) {
            $invitationUser = EventInvitationSpecificUsers::find()
                ->innerJoin('user', 'event_invitation_specific_users.user_id = user.id')
                ->andWhere(['user.email' => $invitee->email])
                ->andWhere(['event_id' => $event_id])->one();
            if ($invitationUser) {
                $invitationUser->webmeeting_invitee_id = $invitee->id;
                $invitationUser->save(false);
            }
        }
    }

    /**
     * @param $webmeeting
     * @param $user_ids
     * @return array
     */
    public static function prepareUserToImportInWebex($webmeeting, $user_ids)
    {
        $allInvitedUsers = User::find()->andWhere(['id' => $user_ids])->select('user.id');
        $userEmailsForInvitee = array_values(
            ArrayHelper::map(User::find()
                ->andWhere(['id' => $allInvitedUsers])->select('email')->asArray()->all(),
                'email', 'email'
            )
        );
        $invitees = \open20\webmeeting\utility\WebMeetingUtility::prepareInviteeForBulkInsert($userEmailsForInvitee, false);
        $apiParams = [
            'meetingId' => $webmeeting->meeting_id,
            'hostEmail' => $webmeeting->host_email,
            'items' => $invitees
        ];
        return $apiParams;
    }


    /**
     * @param $webmeeting
     */
    public static function setHostsWebmeeting($webmeeting)
    {
        $module = \Yii::$app->getModule('events');

        $userHosts = \open20\webmeeting\models\WebMeetingHostUsers::find()
            ->andWhere(['email' => $webmeeting->host_email])
            ->one();

        $webmeeting->invitees = json_decode($webmeeting->invitees);
        $webmeeting->invitees = [
            [
                "email" => $webmeeting->host_email,
                "display_name" => $userHosts->display_name,
                "coHost" => true
            ],
        ];
        if ($module) {
            if (!empty($module->hostsWebmeeting['coHosts'])) {
                $webmeeting->invitees = ArrayHelper::merge($webmeeting->invitees, $module->hostsWebmeeting['coHosts']);
            }
        }
    }

    /**
     * @return bool
     * @throws \yii\base\InvalidConfigException
     */
    public static function isMultilanguageEnabled()
    {
        $moduleCwh = \Yii::$app->getModule('cwh');
        $moduleEvents = \Yii::$app->getModule('events');
        if (!empty($moduleCwh) && !empty($moduleEvents)) {
            $scope = $moduleCwh->getCwhScope();
            if (!empty($scope) && isset($scope['community'])) {
                $communityId = $scope['community'];
                $event = \open20\amos\events\models\Event::find()->andWhere(['community_id' => $communityId])->one();
                if ($event->multilanguage) {
                    return true;
                }
            }
        }
        return false;
    }


    public static function getSidebarPages()
    {

        $menu = [];

        $menu[] = [
            'mainMenu' => [
                'label' => AmosEvents::t('amosevents', 'Metriche'),
                'icon' => '/sprite/material-sprite.svg#ic_show_chart',
                'activeTargetAction' => 'index',
                'activeTargetController' => 'data-analyzer',
                'titleLink' => AmosEvents::t('amosevents', 'Metriche'),
                'url' => '/events/data-analyzer/index'
            ],
            'dinamicSubMenu' => [
                'menuMetriche1' => [
                    'label' => AmosEvents::t('amosevents', 'Tipologie di evento'),
                    'url' => '/events/data-analyzer/analyze-event-types',
                    'activeTargetAction' => 'analyze-event-types',
                    'activeTargetController' => 'data-analyzer',
                    'titleLink' => AmosEvents::t('amosevents', 'Tipologia evento')
                ],
                'menuMetriche2' => [
                    'label' => AmosEvents::t('amosevents', 'Direzioni generali'),
                    'titleLink' => AmosEvents::t('amosevents', 'Direzioni generali'),
                    'url' => '/events/data-analyzer/analyze-event-dg',
                    'activeTargetAction' => 'analyze-event-dg',
                    'activeTargetController' => 'data-analyzer',
                ],
                'menuMetriche3' => [
                    'label' => AmosEvents::t('amosevents', 'Ambito delegato'),
                    'titleLink' => AmosEvents::t('amosevents', 'Ambito delegato'),
                    'url' => '/events/data-analyzer/analyze-event-delegations',
                    'activeTargetAction' => 'analyze-event-delegations',
                    'activeTargetController' => 'data-analyzer',
                ],
                'menuMetriche4' => [
                    'label' => AmosEvents::t('amosevents', 'Tag informativi'),
                    'titleLink' => AmosEvents::t('amosevents', 'Tag informativi'),
                    'url' => '/events/data-analyzer/analyze-event-preference-tags',
                    'activeTargetAction' => 'analyze-event-preference-tags',
                    'activeTargetController' => 'data-analyzer',
                ],
                'menuMetriche5' => [
                    'label' => AmosEvents::t('amosevents', 'Partecipazione evento'),
                    'titleLink' => AmosEvents::t('amosevents', 'Partecipazione evento'),
                    'url' => '/events/data-analyzer/analyze-event-participants',
                    'activeTargetAction' => 'analyze-event-participants',
                    'activeTargetController' => 'data-analyzer',
                ],
            ],

        ];

        $menu [] = [
            'mainMenu' => [
                'label' => AmosEvents::t('formezprojects', 'Dati utenti'),
                'icon' => '/sprite/material-sprite.svg#ic_group',
                'activeTargetAction' => 'user-access',
                'activeTargetController' => 'data-analyzer',
                'titleLink' => AmosEvents::t('formezprojects', 'Dati utenti'),
                'url' => '/events/data-analyzer/user-access'
            ],
            'dinamicSubMenu' => [
                'menuMetriche1' => [
                    'label' => AmosEvents::t('amosevents', 'Accessi backend'),
                    'url' => '/events/data-analyzer/user-access',
                    'activeTargetAction' => 'user-access',
                    'activeTargetController' => 'data-analyzer',
                    'titleLink' => AmosEvents::t('amosevents', 'Accessi backend')
                ],
                'menuMetriche2' => [
                    'label' => AmosEvents::t('amosevents', 'Accessi frontend'),
                    'titleLink' => AmosEvents::t('amosevents', 'Accessi frontend'),
                    'url' => '/events/data-analyzer/user-access-frontend',
                    'activeTargetAction' => 'user-access-frontend',
                    'activeTargetController' => 'data-analyzer',
                ],
                'menuMetriche3' => [
                    'label' => AmosEvents::t('amosevents', 'Errori'),
                    'titleLink' => AmosEvents::t('amosevents', 'Errori'),
                    'url' => '/events/data-analyzer/bounce-errors',
                    'activeTargetAction' => 'bounce-errors',
                    'activeTargetController' => 'data-analyzer',
                ],
                'menuMetriche5' => [
                    'label' => AmosEvents::t('amosevents', 'Utenti inattivi'),
                    'titleLink' => AmosEvents::t('amosevents', 'Utenti inattivi'),
                    'url' => '/events/data-analyzer/inactive-users',
                    'activeTargetAction' => 'inactive-users',
                    'activeTargetController' => 'data-analyzer',
                ],
                'menuMetriche6' => [
                    'label' => AmosEvents::t('amosevents', 'Utenti registrati'),
                    'titleLink' => AmosEvents::t('amosevents', 'Utentu registrati'),
                    'url' => '/events/data-analyzer/direzioni-generali-users',
                    'activeTargetAction' => 'direzioni-generali-users',
                    'activeTargetController' => 'data-analyzer',
                ]

            ]
        ];


        return $menu;
    }


    /**
     * @param $event_id
     * @param null $user_id
     * @return array
     */
    public static function getCompanions($event_id, $user_id = null)
    {
        if (is_null($user_id)) {
            $user_id = \Yii::$app->user->id;
        }
        $companions = [];
        $invitation = EventInvitation::find()->andWhere(['event_id' => $event_id, 'user_id' => $user_id])->one();
        if ($invitation) {
            $companions = $invitation->companions;
        }
        return $companions;
    }

    /**
     * @return bool
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    public function importInactiveUsers()
    {
        if (Yii::$app->request->post() && (isset($_FILES['import-file']['tmp_name']) && (!empty($_FILES['import-file']['tmp_name'])))) {
            $inputFileName = $_FILES['import-file']['tmp_name'];
            $path = $_FILES['import-file']['name'];
            $ext = pathinfo($path, PATHINFO_EXTENSION);
            $allowedExts = array("csv", "xlsx", "xls");
            $i = 0;

            EventInactiveUsersMailup::deleteAll();

            if (in_array($ext, $allowedExts)) {
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($inputFileName);
                $worksheet = $spreadsheet->getActiveSheet();
                $highestRow = $worksheet->getHighestDataRow();
                for ($r = 2; $r <= $highestRow; ++$r) { // Skip first row
                    $firstValue = $worksheet->getCellByColumnAndRow(1, $r)->getValue();
                    if (empty($firstValue) || $firstValue == '') {
                        break;
                    }
                    $i++;
                    $inactiveUser = new EventInactiveUsersMailup();
                    $email = trim($worksheet->getCellByColumnAndRow(1, $r)->getValue());
                    $inactiveUser->email = $email;
                    $inactiveUser->save(false);
                }
                \Yii::$app->session->addFlash('success', AmosEvents::t('amosevents', 'Importati {x} elementi'), [
                    'x' => $i
                ]);
                return true;
            }
        }
        return false;
    }


    /**
     * @param $day
     * @return string
     */
    public static function getLabelSearchDate($day)
    {
        switch ($day) {
            case 'all':
                $label = AmosEvents::t('amosevents', "tutti");
                break;
            case 'today':
                $label = AmosEvents::t('amosevents', "oggi");
                break;
            case 'next_weekend':
                $label = AmosEvents::t('amosevents', "prossimo weekend");
                break;
            case 'this_week':
                $label = AmosEvents::t('amosevents', "questa settimana");
                break;
            case 'this_month':
                $label = AmosEvents::t('amosevents', "questo mese");
                break;
            case 'closed':
                $label = AmosEvents::t('amosevents', "eventi conclusi");
                break;
            case 'highlights':
                $label = AmosEvents::t('amosevents', "<span class=\"title-evidence\">in evidenza</span>");
                break;
        }
        return $label;
    }

    /**
     * @param int $lang_id
     * @return array
     */
    public static function getCmsTemplates($lang_id = 1)
    {
        return (new Query())
            ->select(['cms_nav.id as id', 'nav_container_id', 'parent_nav_id', 'is_hidden', 'is_offline', 'is_draft', 'is_home', 'cms_nav_item.title as title'])
            ->from('cms_nav')
            ->leftJoin('cms_nav_item', 'cms_nav.id=cms_nav_item.nav_id')
            ->orderBy('cms_nav.sort_index ASC')
            ->where([
                'cms_nav_item.lang_id' => $lang_id,
                'cms_nav.is_deleted' => false,
                'cms_nav.is_draft' => true,

            ])
            ->andWhere(['NOT LIKE', 'cms_nav_item.title', 'TEMPLATE'])
            ->all();
    }

    /**
     * @param $model
     * @param $templates
     * @return string
     */
    public static function getBackgroundClassCmsTemplate($model, $templates)
    {
        $class = '';
        foreach ($templates as $template) {
            if ($model->eventLanding->luya_template_id == $template['id']) {
                $class = \open20\amos\events\utility\EventsUtility::getClassPreviewLanding($template['title']);
            }
        }
        return $class;
    }

    /**
     * @param $model
     * @return mixed
     */
    public static function getTotRegisteredUser($model)
    {
        $totConfirmedCompanions = $model->getTotConfirmedParticipantsCompanions();
        $totWaitingUsers = $model->getTotWaitingParticipants();
        $totParticipants = $model->getTotConfirmedParticipants();
        return $totParticipants + $totWaitingUsers + $totConfirmedCompanions;

    }

    /**
     * @param $model
     * @throws ForbiddenHttpException
     */
    public static function throwAccessDeniedSuspended($model)
    {
        if ($model->status == Event::EVENTS_WORKFLOW_STATUS_SUSPENDED) {
            throw new ForbiddenHttpException('Accesso negato');
        }
    }

    /**
     *
     */
    public static function flushCache()
    {
        $client = new Client();
        $url_front = \Yii::$app->params['platform']['frontendUrl'] . '/api/1/flush-cache';

        try {
            $response = $client->createRequest()
                ->setMethod('POST')
                ->setUrl($url_front)
                ->setHeaders(['X-Requested-With' => 'XMLHttpRequest'])
                ->setOptions([
                    'timeout' => 600, // set timeout to 600 seconds for the case server is not responding
                ])
                ->send();
        } catch (\Exception $e) {

        }

        return $response->isOk;

    }

    /**
     * @param $eventId
     * @return mixed
     */
    public static function getChildrenEvent($eventId)
    {
        $query = Event::find()
            ->andWhere(['event_id' => $eventId])
            ->andWhere(['is_time_period' => 0])
            ->andWhere(['status' => Event::EVENTS_WORKFLOW_STATUS_PUBLISHED])
            ->orderBy('begin_date_hour ASC');
        return $query;
    }

    /**
     * @param $model
     * @return void
     */
    public static function correctPurify($model)
    {
        foreach ($model->attributes as $attribute => $value) {
            $model->$attribute = str_replace('%7BURL_LANDING%7D', '{URL_LANDING}', $value);
        }
    }

    public static function updateLuyaPageTimestampForCache($landing)
    {
        $currentTime = date('Y-m-d H:i:s');
        $currentTimestamp = strtotime("now");
        \Yii::$app->db->createCommand("update cms_nav_item set timestamp_update = '$currentTimestamp'  where nav_id = {$landing->luya_page_id}")->execute();
        \Yii::$app->db->createCommand("update event_landing set updated_at = '$currentTime'  where event_landing.id = {$landing->id}")->execute();
    }

    /**
     * @param $crop
     * @return int[]|mixed
     */
    public static function getImageCrops($crop)
    {
        switch ($crop) {
            case 'item':
                $crop = ['width' => 400, 'quality' => 80];
                break;
            case 'big':
                $crop = ['width' => 1600, 'quality' => 80];
                break;
//            default:
//                $crop = 'original';
        }
        return $crop;
    }

    /**
     * @return array|null
     */
    public static function eventConfigCachePage()
    {
        $disableCache = false;
        if (!empty(\Yii::$app->params['disablePageCache'])) {
            $disableCache = true;
        }

        $pagesWithEnableCache = ['/events/event/search-events'];
        $actionDependingFromUser = [];

        $url = \Yii::$app->request->getUrl();
        $parseUrl = parse_url($url);
        if (!empty($parseUrl['path'])) {
            $url = $parseUrl['path'];
        }

        $sql = Event::CACHE_SQL_DEPENDENCY;
        if (in_array($url, $pagesWithEnableCache)) {
            //le action che dipendono dall'utente devono avere anche l'utente come variazione
            return [
                'class' => ResponseCache::class,
                'variations' => [
                    Yii::$app->request->url,
                    Yii::$app->language,
                    Yii::$app->request->get(),
                    Yii::$app->request->post(),
//                    Yii::$app->user->isGuest && !in_array($url, $actionDependingFromUser) ? 'guest' : \Yii::$app->user->id
                ],
                'cache' => 'landingCache',
                'duration' => Event::CACHE_DURATION,
                'dependency' => [
                    'class' => 'yii\caching\DbDependency',
                    'sql' => $sql
                ],
                'enabled' => !$disableCache && in_array($url, $pagesWithEnableCache),
            ];
        }
        return null;
    }

    public static function setCookieForCache()
    {
        $cookies = Yii::$app->response->cookies;

        $cookies->add(new \yii\web\Cookie([
            'name' => 'updated_profile',
            'value' => date('Y-m-d H:i:s'),
            'domain' => \Yii::$app->params['domain']
        ]));
    }
}