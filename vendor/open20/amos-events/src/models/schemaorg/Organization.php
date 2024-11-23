<?php

namespace open20\amos\events\models\schemaorg;

use simialbi\yii2\schemaorg\models\Model;

/**
 * An organization such as a school, NGO, corporation, club, etc.
 *
 */
class Organization extends Model
{
    /**
     * The geographic area where the service is provided.
     *
     * @var AdministrativeArea|Place|GeoShape
     */
    public $serviceArea;

    /**
     * A person who founded this organization.
     *
     * @var Person
     */
    public $founder;

    /**
     * The International Standard of Industrial Classification of All Economic
     * Activities (ISIC), Revision 4 code for a particular organization, business
     * person, or place.
     *
     * @var string
     */
    public $isicV4;

    /**
     * Points-of-Sales operated by the organization or person.
     *
     * @var Place
     */
    public $hasPOS;

    /**
     * The [Global Location Number](http://www.gs1.org/gln) (GLN, sometimes also
     * referred to as International Location Number or ILN) of the respective
     * organization, person, or place. The GLN is a 13-digit number used to
     * identify parties and physical locations.
     *
     * @var string
     */
    public $globalLocationNumber;

    /**
     * A member of an Organization or a ProgramMembership. Organizations can be
     * members of organizations; ProgramMembership is typically for individuals.
     *
     * @var Organization|Person
     */
    public $member;

    /**
     * Of a [[Person]], and less typically of an [[Organization]], to indicate a
     * topic that is known about - suggesting possible expertise but not implying
     * it. We do not distinguish skill levels here, or relate this to educational
     * content, events, objectives or [[JobPosting]] descriptions.
     *
     * @var Thing|string
     */
    public $knowsAbout;

    /**
     * A pointer to products or services offered by the organization or person.
     *
     * @var Offer
     */
    public $makesOffer;

    /**
     * For an [[Organization]] (often but not necessarily a
     * [[NewsMediaOrganization]]), a description of organizational ownership
     * structure; funding and grants. In a news/media setting, this is with
     * particular reference to editorial independence.   Note that the [[funder]]
     * is also available and can be used to make basic funder information
     * machine-readable.
     *
     * @var AboutPage|string|CreativeWork
     */
    public $ownershipFundingInfo;

    /**
     * A person who founded this organization.
     *
     * @var Person
     */
    public $founders;

    /**
     * The official name of the organization, e.g. the registered company name.
     *
     * @var string
     */
    public $legalName;

    /**
     * For a [[NewsMediaOrganization]] or other news-related [[Organization]], a
     * statement about public engagement activities (for news media, the
     * newsroom’s), including involving the public - digitally or otherwise --
     * in coverage decisions, reporting and activities after publication.
     *
     * @var CreativeWork|string
     */
    public $actionableFeedbackPolicy;

    /**
     * The geographic area where a service or offered item is provided.
     *
     * @var string|Place|GeoShape|AdministrativeArea
     */
    public $areaServed;

    /**
     * The larger organization that this organization is a [[subOrganization]] of,
     * if any.
     *
     * @var Organization
     */
    public $parentOrganization;

    /**
     * A slogan or motto associated with the item.
     *
     * @var string
     */
    public $slogan;

    /**
     * A relationship between an organization and a department of that
     * organization, also described as an organization (allowing different urls,
     * logos, opening hours). For example: a store with a pharmacy, or a bakery
     * with a cafe.
     *
     * @var Organization
     */
    public $department;

    /**
     * Keywords or tags used to describe some item. Multiple textual entries in a
     * keywords list are typically delimited by commas, or by repeating the
     * property.
     *
     * @var string|DefinedTerm
     */
    public $keywords;

    /**
     * Review of the item.
     *
     * @var Review
     */
    public $reviews;

    /**
     * An Organization (or ProgramMembership) to which this Person or Organization
     * belongs.
     *
     * @var Organization|ProgramMembership
     */
    public $memberOf;

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
     * Someone working for this organization.
     *
     * @var Person
     */
    public $employee;

    /**
     * An award won by or for this item.
     *
     * @var string
     */
    public $award;

    /**
     * Email address.
     *
     * @var string
     */
    public $email;

    /**
     * A contact point for a person or organization.
     *
     * @var ContactPoint
     */
    public $contactPoints;

    /**
     * For an [[Organization]] (often but not necessarily a
     * [[NewsMediaOrganization]]), a report on staffing diversity issues. In a
     * news context this might be for example ASNE or RTDNA (US) reports, or
     * self-reported.
     *
     * @var Article|string
     */
    public $diversityStaffingReport;

    /**
     * The date that this organization was founded.
     *
     * @var string
     */
    public $foundingDate;

    /**
     * Products owned by the organization or person.
     *
     * @var Product|OwnershipInfo
     */
    public $owns;

    /**
     * Awards won by or for this item.
     *
     * @var string
     */
    public $awards;

    /**
     * A review of the item.
     *
     * @var Review
     */
    public $review;

    /**
     * The date that this organization was dissolved.
     *
     * @var string
     */
    public $dissolutionDate;

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
     * Upcoming or past events associated with this place or organization.
     *
     * @var Event
     */
    public $events;

    /**
     * A pointer to products or services sought by the organization or person
     * (demand).
     *
     * @var Demand
     */
    public $seeks;

    /**
     * People working for this organization.
     *
     * @var Person
     */
    public $employees;

    /**
     * For an [[Organization]] (typically a [[NewsMediaOrganization]]), a
     * statement about policy on use of unnamed sources and the decision process
     * required.
     *
     * @var CreativeWork|string
     */
    public $unnamedSourcesPolicy;

    /**
     * A relationship between two organizations where the first includes the
     * second, e.g., as a subsidiary. See also: the more specific 'department'
     * property.
     *
     * @var Organization
     */
    public $subOrganization;

    /**
     * The place where the Organization was founded.
     *
     * @var Place
     */
    public $foundingLocation;

    /**
     * A person or organization that supports (sponsors) something through some
     * kind of financial contribution.
     *
     * @var Organization|Person
     */
    public $funder;

    /**
     * An organization identifier as defined in ISO 6523(-1). Note that many
     * existing organization identifiers such as
     * [leiCode](http://schema.org/leiCode), [duns](http://schema.org/duns) and
     * [vatID](http://schema.org/vatID) can be expressed as an ISO 6523 identifier
     * by setting the ICD part of the ISO 6523 identifier accordingly. 
     *
     * @var string
     */
    public $iso6523Code;

    /**
     * Statement on diversity policy by an [[Organization]] e.g. a
     * [[NewsMediaOrganization]]. For a [[NewsMediaOrganization]], a statement
     * describing the newsroom’s diversity policy on both staffing and sources,
     * typically providing staffing data.
     *
     * @var string|CreativeWork
     */
    public $diversityPolicy;

    /**
     * Specifies a MerchantReturnPolicy that may be applicable.
     *
     * @var MerchantReturnPolicy
     */
    public $hasMerchantReturnPolicy;

    /**
     * Upcoming or past event associated with this place, organization, or action.
     *
     * @var Event
     */
    public $event;

    /**
     * The Dun & Bradstreet DUNS number for identifying an organization or
     * business person.
     *
     * @var string
     */
    public $duns;

    /**
     * Alumni of an organization.
     *
     * @var Person
     */
    public $alumni;

    /**
     * Statement about ethics policy, e.g. of a [[NewsMediaOrganization]]
     * regarding journalistic and publishing practices, or of a [[Restaurant]], a
     * page describing food source policies. In the case of a
     * [[NewsMediaOrganization]], an ethicsPolicy is typically a statement
     * describing the personal, organizational, and corporate standards of
     * behavior expected by the organization.
     *
     * @var CreativeWork|string
     */
    public $ethicsPolicy;

    /**
     * An organization identifier that uniquely identifies a legal entity as
     * defined in ISO 17442.
     *
     * @var string
     */
    public $leiCode;

    /**
     * The Value-added Tax ID of the organization or person.
     *
     * @var string
     */
    public $vatID;

    /**
     * Of a [[Person]], and less typically of an [[Organization]], to indicate a
     * known language. We do not distinguish skill levels or
     * reading/writing/speaking/signing here. Use language codes from the [IETF
     * BCP 47 standard](http://tools.ietf.org/html/bcp47).
     *
     * @var string|Language
     */
    public $knowsLanguage;

    /**
     * For an [[Organization]] (e.g. [[NewsMediaOrganization]]), a statement
     * describing (in news media, the newsroom’s) disclosure and correction
     * policy for errors.
     *
     * @var string|CreativeWork
     */
    public $correctionsPolicy;

    /**
     * An associated logo.
     *
     * @var ImageObject|string
     */
    public $logo;

    /**
     * A credential awarded to the Person or Organization.
     *
     * @var EducationalOccupationalCredential
     */
    public $hasCredential;

    /**
     * Physical address of the item.
     *
     * @var string|PostalAddress
     */
    public $address;

    /**
     * The brand(s) associated with a product or service, or the brand(s)
     * maintained by an organization or business person.
     *
     * @var Brand|Organization
     */
    public $brand;

    /**
     * nonprofitStatus indicates the legal status of a non-profit organization in
     * its primary place of business.
     *
     * @var NonprofitType
     */
    public $nonprofitStatus;

    /**
     * A contact point for a person or organization.
     *
     * @var ContactPoint
     */
    public $contactPoint;

    /**
     * Indicates an OfferCatalog listing for this Organization, Person, or
     * Service.
     *
     * @var OfferCatalog
     */
    public $hasOfferCatalog;

    /**
     * A member of this organization.
     *
     * @var Organization|Person
     */
    public $members;

    /**
     * The overall rating, based on a collection of reviews or ratings, of the
     * item.
     *
     * @var AggregateRating
     */
    public $aggregateRating;

    /**
     * The fax number.
     *
     * @var string
     */
    public $faxNumber;

    /**
     * The telephone number.
     *
     * @var string
     */
    public $telephone;

    /**
     * The Tax / Fiscal ID of the organization or person, e.g. the TIN in the US
     * or the CIF/NIF in Spain.
     *
     * @var string
     */
    public $taxID;

    /**
     * The North American Industry Classification System (NAICS) code for a
     * particular organization or business person.
     *
     * @var string
     */
    public $naics;

    /**
     * The location of, for example, where an event is happening, where an
     * organization is located, or where an action takes place.
     *
     * @var Place|string|VirtualLocation|PostalAddress
     */
    public $location;

    /**
     * The number of employees in an organization, e.g. business.
     *
     * @var QuantitativeValue
     */
    public $numberOfEmployees;

    /**
     * A person or organization that supports a thing through a pledge, promise,
     * or financial contribution. E.g. a sponsor of a Medical Study or a corporate
     * sponsor of an event.
     *
     * @var Organization|Person
     */
    public $sponsor;

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
