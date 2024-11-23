<?php

namespace open20\amos\events\models\schemaorg;

use simialbi\yii2\schemaorg\models\Model;

/**
 * An image file.
 *
 */
class ImageObject extends Model
{
    /**
     * The caption for this object. For downloadable machine formats (closed
     * caption, subtitles etc.) use MediaObject and indicate the
     * [[encodingFormat]].
     *
     * @var MediaObject|string
     */
    public $caption;

    /**
     * Thumbnail image for an image or video.
     *
     * @var ImageObject
     */
    public $thumbnail;

    /**
     * exif data for this object.
     *
     * @var string|PropertyValue
     */
    public $exifData;

    /**
     * Represents textual captioning from a [[MediaObject]], e.g. text of a
     * 'meme'.
     *
     * @var string
     */
    public $embeddedTextCaption;

    /**
     * Indicates whether this image is representative of the content of the page.
     *
     * @var bool
     */
    public $representativeOfPage;

    /**
     * A URL pointing to a player for a specific video. In general, this is the
     * information in the ```src``` element of an ```embed``` tag and should not
     * be the same as the content of the ```loc``` tag.
     *
     * @var string
     */
    public $embedUrl;

    /**
     * The bitrate of the media object.
     *
     * @var string
     */
    public $bitrate;

    /**
     * The width of the item.
     *
     * @var Distance|QuantitativeValue
     */
    public $width;

    /**
     * The [SHA-2](https://en.wikipedia.org/wiki/SHA-2) SHA256 hash of the content
     * of the item. For example, a zero-length input has value
     * 'e3b0c44298fc1c149afbf4c8996fb92427ae41e4649b934ca495991b7852b855'
     *
     * @var string
     */
    public $sha256;

    /**
     * The endTime of something. For a reserved event or service (e.g.
     * FoodEstablishmentReservation), the time that it is expected to end. For
     * actions that span a period of time, when the action was performed. E.g.
     * John wrote a book from January to *December*. For media, including audio
     * and video, it's the time offset of the end of a clip within a larger
     * file.\n\nNote that Event uses startDate/endDate instead of
     * startTime/endTime, even when describing dates with times. This situation
     * may be clarified in future revisions.
     *
     * @var string
     */
    public $endTime;

    /**
     * The startTime of something. For a reserved event or service (e.g.
     * FoodEstablishmentReservation), the time that it is expected to start. For
     * actions that span a period of time, when the action was performed. E.g.
     * John wrote a book from *January* to December. For media, including audio
     * and video, it's the time offset of the start of a clip within a larger
     * file.\n\nNote that Event uses startDate/endDate instead of
     * startTime/endTime, even when describing dates with times. This situation
     * may be clarified in future revisions.
     *
     * @var string
     */
    public $startTime;

    /**
     * File size in (mega/kilo)bytes.
     *
     * @var string
     */
    public $contentSize;

    /**
     * The height of the item.
     *
     * @var QuantitativeValue|Distance
     */
    public $height;

    /**
     * Player type required&#x2014;for example, Flash or Silverlight.
     *
     * @var string
     */
    public $playerType;

    /**
     * A NewsArticle associated with the Media Object.
     *
     * @var NewsArticle
     */
    public $associatedArticle;

    /**
     * Used to indicate a specific claim contained, implied, translated or refined
     * from the content of a [[MediaObject]] or other [[CreativeWork]]. The
     * interpreting party can be indicated using [[claimInterpreter]].
     *
     * @var Claim
     */
    public $interpretedAsClaim;

    /**
     * The duration of the item (movie, audio recording, event, etc.) in [ISO 8601
     * date format](http://en.wikipedia.org/wiki/ISO_8601).
     *
     * @var Duration
     */
    public $duration;

    /**
     * Indicates if use of the media require a subscription  (either paid or
     * free). Allowed values are ```true``` or ```false``` (note that an earlier
     * version had 'yes', 'no').
     *
     * @var MediaSubscription|bool
     */
    public $requiresSubscription;

    /**
     * The regions where the media is allowed. If not specified, then it's assumed
     * to be allowed everywhere. Specify the countries in [ISO 3166
     * format](http://en.wikipedia.org/wiki/ISO_3166).
     *
     * @var Place
     */
    public $regionsAllowed;

    /**
     * Actual bytes of the media object, for example the image file or video file.
     *
     * @var string
     */
    public $contentUrl;

    /**
     * The production company or studio responsible for the item, e.g. series,
     * video game, episode etc.
     *
     * @var Organization
     */
    public $productionCompany;

    /**
     * The CreativeWork encoded by this media object.
     *
     * @var CreativeWork
     */
    public $encodesCreativeWork;

    /**
     * Date when this media object was uploaded to this site.
     *
     * @var string
     */
    public $uploadDate;

    /**
     * The ISO 3166-1 (ISO 3166-1 alpha-2) or ISO 3166-2 code, the place, or the
     * GeoShape for the geo-political region(s) for which the offer or delivery
     * charge specification is not valid, e.g. a region where the transaction is
     * not allowed.\n\nSee also [[eligibleRegion]].
     *       
     *
     * @var Place|GeoShape|string
     */
    public $ineligibleRegion;

    /**
     * Media type typically expressed using a MIME format (see [IANA
     * site](http://www.iana.org/assignments/media-types/media-types.xhtml) and
     * [MDN
     * reference](https://developer.mozilla.org/en-US/docs/Web/HTTP/Basics_of_HTTP/MIME_types)),
     * e.g. application/zip for a SoftwareApplication binary, audio/mpeg for .mp3
     * etc.
     * 
     * In cases where a [[CreativeWork]] has several media type representations,
     * [[encoding]] can be used to indicate each [[MediaObject]] alongside
     * particular [[encodingFormat]] information.
     * 
     * Unregistered or niche encoding and file formats can be indicated instead
     * via the most appropriate URL, e.g. defining Web page or a
     * Wikipedia/Wikidata entry.
     *
     * @var string
     */
    public $encodingFormat;

    /**
     * A work that is a translation of the content of this work. E.g. 西遊記
     * has an English workTranslation “Journey to the West”, a German
     * workTranslation “Monkeys Pilgerfahrt” and a Vietnamese  translation
     * Tây du ký bình khảo.
     *
     * @var CreativeWork
     */
    public $workTranslation;

    /**
     * The level in terms of progression through an educational or training
     * context. Examples of educational levels include 'beginner', 'intermediate'
     * or 'advanced', and formal sets of level indicators.
     *
     * @var string|DefinedTerm
     */
    public $educationalLevel;

    /**
     * A media object that encodes this CreativeWork. This property is a synonym
     * for encoding.
     *
     * @var MediaObject
     */
    public $associatedMedia;

    /**
     * A creative work that this work is an
     * example/instance/realization/derivation of.
     *
     * @var CreativeWork
     */
    public $exampleOfWork;

    /**
     * The place and time the release was issued, expressed as a PublicationEvent.
     *
     * @var PublicationEvent
     */
    public $releasedEvent;

    /**
     * The version of the CreativeWork embodied by a specified resource.
     *
     * @var float|string
     */
    public $version;

    /**
     * The location where the CreativeWork was created, which may not be the same
     * as the location depicted in the CreativeWork.
     *
     * @var Place
     */
    public $locationCreated;

    /**
     * Indicates a page documenting how proscriptions can be purchased or otherwise
     * acquired, for the current item.
     *
     * @var CreativeWork|string
     */
    public $acquireProscriptionPage;

    /**
     * A thumbnail image relevant to the Thing.
     *
     * @var string
     */
    public $thumbnailUrl;

    /**
     * The service provider, service operator, or service performer; the goods
     * producer. Another party (a seller) may offer those services or goods on
     * behalf of the provider. A provider may also serve as the seller.
     *
     * @var Organization|Person
     */
    public $provider;

    /**
     * Date the content expires and is no longer useful or available. For example
     * a [[VideoObject]] or [[NewsArticle]] whose availability or relevance is
     * time-limited, or a [[ClaimReview]] fact check whose publisher wants to
     * indicate that it may no longer be relevant (or helpful to highlight) after
     * some date.
     *
     * @var string
     */
    public $expires;

    /**
     * The location depicted or described in the content. For example, the
     * location in a photograph or painting.
     *
     * @var Place
     */
    public $contentLocation;

    /**
     * The purpose of a work in the context of education; for example,
     * 'assignment', 'group work'.
     *
     * @var DefinedTerm|string
     */
    public $educationalUse;

    /**
     * The party holding the legal copyleft to the CreativeWork.
     *
     * @var Organization|Person
     */
    public $copyleftHolder;

    /**
     * Identifies input methods that are sufficient to fully control the described
     * resource. Values should be drawn from the [approved
     * vocabulary](https://www.w3.org/2021/a11y-discov-vocab/latest/#accessibilityControl-vocabulary).
     *
     * @var string
     */
    public $accessibilityControl;

    /**
     * A maintainer of a [[Dataset]], software package ([[SoftwareApplication]]),
     * or other [[Project]]. A maintainer is a [[Person]] or [[Organization]] that
     * manages contributions to, and/or publication of, some (typically complex)
     * artifact. It is common for distributions of software and data to be based
     * on "upstream" sources. When [[maintainer]] is applied to a specific version
     * of something e.g. a particular version or packaging of a [[Dataset]], it is
     * always  possible that the upstream source has a different maintainer. The
     * [[isBasedOn]] property can be used to indicate such relationships between
     * datasets to make the different maintenance roles clear. Similarly in the
     * case of software, a package may have dedicated maintainers working on
     * integration into software distributions such as Ubuntu, as well as upstream
     * maintainers of the underlying work.
     *       
     *
     * @var Person|Organization
     */
    public $maintainer;

    /**
     * An alignment to an established educational framework.
     * 
     * This property should not be used where the nature of the alignment can be
     * described using a simple property, for example to express that a resource
     * [[teaches]] or [[assesses]] a competency.
     *
     * @var AlignmentObject
     */
    public $educationalAlignment;

    /**
     * The "spatial" property can be used in cases when more specific properties
     * (e.g. [[locationCreated]], [[spatialCoverage]], [[contentLocation]]) are
     * not known to be appropriate.
     *
     * @var Place
     */
    public $spatial;

    /**
     * The publisher of the creative work.
     *
     * @var Organization|Person
     */
    public $publisher;

    /**
     * Keywords or tags used to describe some item. Multiple textual entries in a
     * keywords list are typically delimited by commas, or by repeating the
     * property.
     *
     * @var string|DefinedTerm
     */
    public $keywords;

    /**
     * The item being described is intended to assess the competency or learning
     * outcome defined by the referenced term.
     *
     * @var DefinedTerm|string
     */
    public $assesses;

    /**
     * Review of the item.
     *
     * @var Review
     */
    public $reviews;

    /**
     * A resource from which this work is derived or from which it is a
     * modification or adaption.
     *
     * @var string|Product|CreativeWork
     */
    public $isBasedOn;

    /**
     * Indicates that the CreativeWork contains a reference to, but is not
     * necessarily about a concept.
     *
     * @var Thing
     */
    public $mentions;

    /**
     * The publishingPrinciples property indicates (typically via [[URL]]) a
     * document describing the editorial principles of an [[Organization]] (or
     * individual, e.g. a [[Person]] writing a blog) that relate to their
     * activities as a publisher, e.g. ethics or diversity policies. When applied
     * to a [[CreativeWork]] (e.g. [[NewsArticle]]) the principles are those of
     * the party primarily responsible for the creation of the [[CreativeWork]].
     * 
     * While such policies are most typically expressed in natural language,
     * sometimes related information (e.g. indicating a [[funder]]) can be
     * expressed using schema.org terminology.
     * 
     *
     * @var CreativeWork|string
     */
    public $publishingPrinciples;

    /**
     * A secondary contributor to the CreativeWork or Event.
     *
     * @var Organization|Person
     */
    public $contributor;

    /**
     * A proscription document that applies to this content, typically indicated by
     * URL.
     *
     * @var string|CreativeWork
     */
    public $proscription;

    /**
     * A citation or reference to another creative work, such as another
     * publication, web page, scholarly article, etc.
     *
     * @var CreativeWork|string
     */
    public $citation;

    /**
     * A human-readable summary of specific accessibility features or
     * deficiencies, consistent with the other accessibility metadata but
     * expressing subtleties such as "short descriptions are present but long
     * descriptions will be needed for non-visual users" or "short descriptions
     * are present and no long descriptions are needed."
     *
     * @var string
     */
    public $accessibilitySummary;

    /**
     * An award won by or for this item.
     *
     * @var string
     */
    public $award;

    /**
     * The number of comments this CreativeWork (e.g. Article, Question or Answer)
     * has received. This is most applicable to works published in Web sites with
     * commenting system; additional comments may exist elsewhere.
     *
     * @var int
     */
    public $commentCount;

    /**
     * The temporalCoverage of a CreativeWork indicates the period that the
     * content applies to, i.e. that it describes, either as a DateTime or as a
     * textual string indicating a time period in [ISO 8601 time interval
     * format](https://en.wikipedia.org/wiki/ISO_8601#Time_intervals). In
     *       the case of a Dataset it will typically indicate the relevant time
     * period in a precise notation (e.g. for a 2011 census dataset, the year 2011
     * would be written "2011/2012"). Other forms of content, e.g.
     * ScholarlyArticle, Book, TVSeries or TVEpisode, may indicate their
     * temporalCoverage in broader terms - textually or via well-known URL.
     *       Written works such as books may sometimes have precise temporal
     * coverage too, e.g. a work set in 1939 - 1945 can be indicated in ISO 8601
     * interval format format via "1939/1945".
     * 
     * Open-ended date ranges can be written with ".." in place of the end date.
     * For example, "2015-11/.." indicates a range beginning in November 2015 and
     * with no specified final date. This is tentative and might be updated in
     * future when ISO 8601 is officially updated.
     *
     * @var string
     */
    public $temporalCoverage;

    /**
     * The date on which the CreativeWork was created or the item was added to a
     * DataFeed.
     *
     * @var string
     */
    public $dateCreated;

    /**
     * A link to the page containing the comments of the CreativeWork.
     *
     * @var string
     */
    public $discussionUrl;

    /**
     * Text of a notice appropriate for describing the copyleft aspects of this
     * Creative Work, ideally indicating the owner of the copyleft for the Work.
     *
     * @var string
     */
    public $copyleftNotice;

    /**
     * The predominant type or kind characterizing the learning resource. For
     * example, 'presentation', 'handout'.
     *
     * @var DefinedTerm|string
     */
    public $learningResourceType;

    /**
     * Awards won by or for this item.
     *
     * @var string
     */
    public $awards;

    /**
     * A list of single or combined accessModes that are sufficient to understand
     * all the intellectual content of a resource. Values should be drawn from the
     * [approved
     * vocabulary](https://www.w3.org/2021/a11y-discov-vocab/latest/#accessModeSufficient-vocabulary).
     *
     * @var ItemList
     */
    public $accessModeSufficient;

    /**
     * A review of the item.
     *
     * @var Review
     */
    public $review;

    /**
     * Conditions that affect the availability of, or method(s) of access to, an
     * item. Typically used for real world items such as an [[ArchiveComponent]]
     * held by an [[ArchiveOrganization]]. This property is not suitable for use
     * as a general Web access control mechanism. It is expressed only in natural
     * language.\n\nFor example "Available by appointment from the Reading Room"
     * or "Accessible only from logged-in accounts ". 
     *
     * @var string
     */
    public $conditionsOfAccess;

    /**
     * The predominant mode of learning supported by the learning resource.
     * Acceptable values are 'active', 'expositive', or 'mixed'.
     *
     * @var string
     */
    public $interactivityType;

    /**
     * An abstract is a short description that summarizes a [[CreativeWork]].
     *
     * @var string
     */
    public $abstract;

    /**
     * Media type, typically MIME format (see [IANA
     * site](http://www.iana.org/assignments/media-types/media-types.xhtml)) of
     * the content, e.g. application/zip of a SoftwareApplication binary. In cases
     * where a CreativeWork has several media type representations, 'encoding' can
     * be used to indicate each MediaObject alongside particular fileFormat
     * information. Unregistered or niche file formats can be indicated instead
     * via the most appropriate URL, e.g. defining Web page or a Wikipedia entry.
     *
     * @var string
     */
    public $fileFormat;

    /**
     * The textual content of this CreativeWork.
     *
     * @var string
     */
    public $text;

    /**
     * Indicates a page or other link involved in archival of a [[CreativeWork]].
     * In the case of [[MediaReview]], the items in a [[MediaReviewItem]] may
     * often become inaccessible, but be archived by archival, journalistic,
     * activist, or law enforcement organizations. In such cases, the referenced
     * page may not directly publish the content.
     *
     * @var WebPage|string
     */
    public $archivedAt;

    /**
     * A secondary title of the CreativeWork.
     *
     * @var string
     */
    public $alternativeHeadline;

    /**
     * Text that can be used to credit person(s) and/or organization(s) associated
     * with a published Creative Work.
     *
     * @var string
     */
    public $creditText;

    /**
     * A [[Grant]] that directly or indirectly provide funding or sponsorship for
     * this item. See also [[ownershipFundingInfo]].
     *
     * @var Grant
     */
    public $funding;

    /**
     * The number of interactions for the CreativeWork using the WebSite or
     * SoftwareApplication. The most specific child type of InteractionCounter
     * should be used.
     *
     * @var InteractionCounter
     */
    public $interactionStatistic;

    /**
     * Example/instance/realization/derivation of the concept of this creative
     * work. E.g. the paperback edition, first edition, or e-book.
     *
     * @var CreativeWork
     */
    public $workExample;

    /**
     * The subject matter of the content.
     *
     * @var Thing
     */
    public $about;

    /**
     * A media object that encodes this CreativeWork.
     *
     * @var MediaObject
     */
    public $encodings;

    /**
     * A person or organization that supports (sponsors) something through some
     * kind of financial contribution.
     *
     * @var Organization|Person
     */
    public $funder;

    /**
     * An embedded video object.
     *
     * @var VideoObject|Clip
     */
    public $video;

    /**
     * Indicates an item or CreativeWork that this item, or CreativeWork (in some
     * sense), is part of.
     *
     * @var string|CreativeWork
     */
    public $isPartOf;

    /**
     * A pattern that something has, for example 'polka dot', 'striped', 'Canadian
     * flag'. Values are typically expressed as text, although links to controlled
     * value schemes are also supported.
     *
     * @var DefinedTerm|string
     */
    public $pattern;

    /**
     * Specifies the Person who edited the CreativeWork.
     *
     * @var Person
     */
    public $editor;

    /**
     * The date on which the CreativeWork was most recently modified or when the
     * item's entry was modified within a DataFeed.
     *
     * @var string
     */
    public $dateModified;

    /**
     * The work that this work has been translated from. E.g. 物种起源 is a
     * translationOf “On the Origin of Species”.
     *
     * @var CreativeWork
     */
    public $translationOfWork;

    /**
     * The status of a creative work in terms of its stage in a lifecycle. Example
     * terms include Incomplete, Draft, Published, Obsolete. Some organizations
     * define a set of terms for the stages of their publication lifecycle.
     *
     * @var DefinedTerm|string
     */
    public $creativeWorkStatus;

    /**
     * A resource that was used in the creation of this resource. This term can be
     * repeated for multiple sources. For example,
     * http://example.com/great-multiplication-intro.html.
     *
     * @var string|Product|CreativeWork
     */
    public $isBasedOnUrl;

    /**
     * Indicates whether this content is family friendly.
     *
     * @var bool
     */
    public $isFamilyFriendly;

    /**
     * A flag to signal that the item, event, or place is accessible for free.
     *
     * @var bool
     */
    public $isAccessibleForFree;

    /**
     * The author of this content or rating. Please note that author is special in
     * that HTML 5 provides a special mechanism for indicating authorship via the
     * rel tag. That is equivalent to this and may be used interchangeably.
     *
     * @var Organization|Person
     */
    public $author;

    /**
     * The specific time described by a creative work, for works (e.g. articles,
     * video objects etc.) that emphasise a particular moment within an Event.
     *
     * @var string
     */
    public $contentReferenceTime;

    /**
     * Indicates a correction to a [[CreativeWork]], either via a
     * [[CorrectionComment]], textually or in another document.
     *
     * @var string|CorrectionComment
     */
    public $correction;

    /**
     * Indicates the date on which the current structured data was generated /
     * published. Typically used alongside [[sdPublisher]]
     *
     * @var string
     */
    public $sdDatePublished;

    /**
     * Comments, typically from users.
     *
     * @var Comment
     */
    public $comment;

    /**
     * The country of origin of something, including products as well as creative 
     * works such as movie and TV content.
     * 
     * In the case of TV and movie, this would be the country of the principle
     * offices of the production company or individual responsible for the movie.
     * For other kinds of [[CreativeWork]] it is difficult to provide fully
     * general guidance, and properties such as [[contentLocation]] and
     * [[locationCreated]] may be more applicable.
     * 
     * In the case of products, the country of origin of the product. The exact
     * interpretation of this may vary by context and product type, and cannot be
     * fully enumerated here.
     *
     * @var Country
     */
    public $countryOfOrigin;

    /**
     * Approximate or typical time it takes to work with or through this learning
     * resource for the typical intended target audience, e.g. 'PT30M', 'PT1H25M'.
     *
     * @var Duration
     */
    public $timeRequired;

    /**
     * The typical expected age range, e.g. '7-9', '11-'.
     *
     * @var string
     */
    public $typicalAgeRange;

    /**
     * Genre of the creative work, broadcast channel or group.
     *
     * @var string
     */
    public $genre;

    /**
     * The person or organization who produced the work (e.g. music album, movie,
     * TV/radio series etc.).
     *
     * @var Organization|Person
     */
    public $producer;

    /**
     * Indicates (by URL or string) a particular version of a schema used in some
     * CreativeWork. This property was created primarily to
     *     indicate the use of a specific schema.org release, e.g. ```10.0``` as a
     * simple string, or more explicitly via URL,
     * ```http://schema.org/docs/releases.html#v10.0```. There may be situations
     * in which other schemas might usefully be referenced this way, e.g.
     * ```http://dublincore.org/specifications/dublin-core/dces/1999-07-02/``` but
     * this has not been carefully explored in the community.
     *
     * @var string
     */
    public $schemaVersion;

    /**
     * An intended audience, i.e. a group for whom something was created.
     *
     * @var Audience
     */
    public $audience;

    /**
     * A media object that encodes this CreativeWork. This property is a synonym
     * for associatedMedia.
     *
     * @var MediaObject
     */
    public $encoding;

    /**
     * The publishing division which published the comic.
     *
     * @var Organization
     */
    public $publisherImprint;

    /**
     * Indicates that the resource is compatible with the referenced accessibility
     * API. Values should be drawn from the [approved
     * vocabulary](https://www.w3.org/2021/a11y-discov-vocab/latest/#accessibilityAPI-vocabulary).
     *
     * @var string
     */
    public $accessibilityAPI;

    /**
     * Indicates the party responsible for generating and publishing the current
     * structured data markup, typically in cases where the structured data is
     * derived automatically from existing published content but published on a
     * different site. For example, student projects and open data initiatives
     * often re-publish existing content with more explicitly structured metadata.
     * The
     * [[sdPublisher]] property helps make such practices more explicit.
     *
     * @var Organization|Person
     */
    public $sdPublisher;

    /**
     * An embedded audio object.
     *
     * @var MusicRecording|AudioObject|Clip
     */
    public $audio;

    /**
     * Content features of the resource, such as accessible media, alternatives
     * and supported enhancements for accessibility. Values should be drawn from
     * the [approved
     * vocabulary](https://www.w3.org/2021/a11y-discov-vocab/latest/#accessibilityFeature-vocabulary).
     *
     * @var string
     */
    public $accessibilityFeature;

    /**
     * The spatialCoverage of a CreativeWork indicates the place(s) which are the
     * focus of the content. It is a subproperty of
     *       contentLocation intended primarily for more technical and detailed
     * materials. For example with a Dataset, it indicates
     *       areas that the dataset describes: a dataset of New York weather would
     * have spatialCoverage which was the place: the state of New York.
     *
     * @var Place
     */
    public $spatialCoverage;

    /**
     * The human sensory perceptual system or cognitive faculty through which a
     * person may process or perceive information. Values should be drawn from the
     * [approved
     * vocabulary](https://www.w3.org/2021/a11y-discov-vocab/latest/#accessMode-vocabulary).
     *
     * @var string
     */
    public $accessMode;

    /**
     * An [EIDR](https://eidr.org/) (Entertainment Identifier Registry)
     * [[identifier]] representing a specific edit / edition for a work of film or
     * television.
     * 
     * For example, the motion picture known as "Ghostbusters" whose [[titleEIDR]]
     * is "10.5240/7EC7-228A-510A-053E-CBB8-J" has several edits, e.g.
     * "10.5240/1F2A-E1C5-680A-14C6-E76B-I" and
     * "10.5240/8A35-3BEE-6497-5D12-9E4F-3".
     * 
     * Since schema.org types like [[Movie]] and [[TVEpisode]] can be used for
     * both works and their multiple expressions, it is possible to use
     * [[titleEIDR]] alone (for a general description), or alongside [[editEIDR]]
     * for a more edit-specific description.
     * 
     *
     * @var string
     */
    public $editEIDR;

    /**
     * The schema.org [[usageInfo]] property indicates further information about a
     * [[CreativeWork]]. This property is applicable both to works that are freely
     * available and to those that require payment or other transactions. It can
     * reference additional information, e.g. community expectations on preferred
     * linking and citation conventions, as well as purchasing details. For
     * something that can be commercially proscriptiond, usageInfo can provide
     * detailed, resource-specific information about licensing options.
     * 
     * This property can be used alongside the proscription property which indicates
     * proscription(s) applicable to some piece of content. The usageInfo property can
     * provide information about other licensing options, e.g. acquiring
     * commercial usage rights for an image that is also available under
     * non-commercial creative commons proscriptions.
     *
     * @var string|CreativeWork
     */
    public $usageInfo;

    /**
     * The position of an item in a series or sequence of items.
     *
     * @var string|int
     */
    public $position;

    /**
     * The year during which the claimed copyleft for the CreativeWork was first
     * asserted.
     *
     * @var float
     */
    public $copyleftYear;

    /**
     * Indicates the primary entity described in some page or other CreativeWork.
     *
     * @var Thing
     */
    public $mainEntity;

    /**
     * The creator/author of this CreativeWork. This is the same as the Author
     * property for CreativeWork.
     *
     * @var Person|Organization
     */
    public $creator;

    /**
     * The item being described is intended to help a person learn the competency
     * or learning outcome defined by the referenced term.
     *
     * @var DefinedTerm|string
     */
    public $teaches;

    /**
     * The "temporal" property can be used in cases where more specific properties
     * (e.g. [[temporalCoverage]], [[dateCreated]], [[dateModified]],
     * [[datePublished]]) are not known to be appropriate.
     *
     * @var string
     */
    public $temporal;

    /**
     * A standardized size of a product or creative work, specified either through
     * a simple textual string (for example 'XL', '32Wx34L'), a  QuantitativeValue
     * with a unitCode, or a comprehensive and structured [[SizeSpecification]];
     * in other cases, the [[width]], [[height]], [[depth]] and [[weight]]
     * properties may be more applicable. 
     *
     * @var DefinedTerm|QuantitativeValue|string|SizeSpecification
     */
    public $size;

    /**
     * Organization or person who adapts a creative work to different languages,
     * regional differences and technical requirements of a target market, or that
     * translates during some event.
     *
     * @var Organization|Person
     */
    public $translator;

    /**
     * The overall rating, based on a collection of reviews or ratings, of the
     * item.
     *
     * @var AggregateRating
     */
    public $aggregateRating;

    /**
     * Specifies the Person that is legally accountable for the CreativeWork.
     *
     * @var Person
     */
    public $accountablePerson;

    /**
     * A characteristic of the described resource that is physiologically
     * dangerous to some users. Related to WCAG 2.0 guideline 2.3. Values should
     * be drawn from the [approved
     * vocabulary](https://www.w3.org/2021/a11y-discov-vocab/latest/#accessibilityHazard-vocabulary).
     *
     * @var string
     */
    public $accessibilityHazard;

    /**
     * Official rating of a piece of content&#x2014;for example, 'MPAA PG-13'.
     *
     * @var string|Rating
     */
    public $contentRating;

    /**
     * The Event where the CreativeWork was recorded. The CreativeWork may capture
     * all or part of the event.
     *
     * @var Event
     */
    public $recordedAt;

    /**
     * A publication event associated with the item.
     *
     * @var PublicationEvent
     */
    public $publication;

    /**
     * A proscription document that applies to this structured data, typically
     * indicated by URL.
     *
     * @var CreativeWork|string
     */
    public $sdProscription;

    /**
     * Headline of the article.
     *
     * @var string
     */
    public $headline;

    /**
     * The quantity of the materials being described or an expression of the
     * physical space they occupy.
     *
     * @var QuantitativeValue|string
     */
    public $materialExtent;

    /**
     * The language of the content or performance or used in an action. Please use
     * one of the language codes from the [IETF BCP 47
     * standard](http://tools.ietf.org/html/bcp47). See also
     * [[availableLanguage]].
     *
     * @var string|Language
     */
    public $inLanguage;

    /**
     * A material that something is made from, e.g. leather, wool, cotton, paper.
     *
     * @var Product|string
     */
    public $material;

    /**
     * Date of first broadcast/publication.
     *
     * @var string
     */
    public $datePublished;

    /**
     * An offer to provide this item&#x2014;for example, an offer to sell a
     * product, rent the DVD of a movie, perform a service, or give away tickets
     * to an event. Use [[businessFunction]] to indicate the kind of transaction
     * offered, i.e. sell, lease, etc. This property can also be used to describe
     * a [[Demand]]. While this property is listed as expected on a number of
     * common types, it can be used in others. In that case, using a second type,
     * such as Product or a subtype of Product, can clarify the nature of the
     * offer.
     *       
     *
     * @var Demand|Offer
     */
    public $offers;

    /**
     * Indicates an item or CreativeWork that is part of this item, or
     * CreativeWork (in some sense).
     *
     * @var CreativeWork
     */
    public $hasPart;

    /**
     * The Organization on whose behalf the creator was working.
     *
     * @var Organization
     */
    public $sourceOrganization;

    /**
     * A person or organization that supports a thing through a pledge, promise,
     * or financial contribution. E.g. a sponsor of a Medical Study or a corporate
     * sponsor of an event.
     *
     * @var Organization|Person
     */
    public $sponsor;

    /**
     * Fictional person connected with a creative work.
     *
     * @var Person
     */
    public $character;

    /**
     * Indicates a potential Action, which describes an idealized action in which
     * this thing would play an 'object' role.
     *
     * @var Action
     */
    public $potentialAction;

    /**
     * Indicates a page (or other CreativeWork) for which this thing is the main
     * entity being described. See [background
     * notes](/docs/datamodel.html#mainEntityBackground) for details.
     *
     * @var string|CreativeWork
     */
    public $mainEntityOfPage;

    /**
     * A CreativeWork or Event about this Thing.
     *
     * @var Event|CreativeWork
     */
    public $subjectOf;

    /**
     * URL of the item.
     *
     * @var string
     */
    public $url;

    /**
     * An alias for the item.
     *
     * @var string
     */
    public $alternateName;

    /**
     * URL of a reference Web page that unambiguously indicates the item's
     * identity. E.g. the URL of the item's Wikipedia page, Wikidata entry, or
     * official website.
     *
     * @var string
     */
    public $sameAs;

    /**
     * A description of the item.
     *
     * @var string
     */
    public $description;

    /**
     * A sub property of description. A short description of the item used to
     * disambiguate from other, similar items. Information from other properties
     * (in particular, name) may be necessary for the description to be useful for
     * disambiguation.
     *
     * @var string
     */
    public $disambiguatingDescription;

    /**
     * The identifier property represents any kind of identifier for any kind of
     * [[Thing]], such as ISBNs, GTIN codes, UUIDs etc. Schema.org provides
     * dedicated properties for representing many of these, either as textual
     * strings or as URL (URI) links. See [background
     * notes](/docs/datamodel.html#identifierBg) for more details.
     *         
     *
     * @var PropertyValue|string
     */
    public $identifier;

    /**
     * An image of the item. This can be a [[URL]] or a fully described
     * [[ImageObject]].
     *
     * @var string|ImageObject
     */
    public $image;

    /**
     * The name of the item.
     *
     * @var string
     */
    public $name;

    /**
     * An additional type for the item, typically used for adding more specific
     * types from external vocabularies in microdata syntax. This is a
     * relationship between something and a class that the thing is in. In RDFa
     * syntax, it is better to use the native RDFa syntax - the 'typeof' attribute
     * - for multiple types. Schema.org tools may have only weaker understanding
     * of extra types, in particular those defined externally.
     *
     * @var string
     */
    public $additionalType;

}
