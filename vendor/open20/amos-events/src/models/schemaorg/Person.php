<?php

namespace open20\amos\events\models\schemaorg;

use simialbi\yii2\schemaorg\models\Model;

/**
 * A person (alive, dead, undead, or fictional).
 *
 */
class Person extends Model
{
    /**
     * A sibling of the person.
     *
     * @var Person
     */
    public $sibling;

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
     * The person's spouse.
     *
     * @var Person
     */
    public $spouse;

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
     * A colleague of the person.
     *
     * @var Person|string
     */
    public $colleague;

    /**
     * An honorific suffix following a Person's name such as M.D./PhD/MSCSW.
     *
     * @var string
     */
    public $honorificSuffix;

    /**
     * Nationality of the person.
     *
     * @var Country
     */
    public $nationality;

    /**
     * An organization that this person is affiliated with. For example, a
     * school/university, a club, or a team.
     *
     * @var Organization
     */
    public $affiliation;

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
     * The height of the item.
     *
     * @var QuantitativeValue|Distance
     */
    public $height;

    /**
     * The most generic bi-directional social/work relation.
     *
     * @var Person
     */
    public $knows;

    /**
     * The most generic familial relation.
     *
     * @var Person
     */
    public $relatedTo;

    /**
     * Organizations that the person works for.
     *
     * @var Organization
     */
    public $worksFor;

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
     * Given name. In the U.S., the first name of a Person.
     *
     * @var string
     */
    public $givenName;

    /**
     * A contact location for a person's place of work.
     *
     * @var ContactPoint|Place
     */
    public $workLocation;

    /**
     * A contact point for a person or organization.
     *
     * @var ContactPoint
     */
    public $contactPoints;

    /**
     * The job title of the person (for example, Financial Manager).
     *
     * @var DefinedTerm|string
     */
    public $jobTitle;

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
     * A child of the person.
     *
     * @var Person
     */
    public $children;

    /**
     * A parent of this person.
     *
     * @var Person
     */
    public $parent;

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
     * A pointer to products or services sought by the organization or person
     * (demand).
     *
     * @var Demand
     */
    public $seeks;

    /**
     * The weight of the product or person.
     *
     * @var QuantitativeValue
     */
    public $weight;

    /**
     * A person or organization that supports (sponsors) something through some
     * kind of financial contribution.
     *
     * @var Organization|Person
     */
    public $funder;

    /**
     * Date of birth.
     *
     * @var string
     */
    public $birthDate;

    /**
     * Date of death.
     *
     * @var string
     */
    public $deathDate;

    /**
     * An additional name for a Person, can be used for a middle name.
     *
     * @var string
     */
    public $additionalName;

    /**
     * The Dun & Bradstreet DUNS number for identifying an organization or
     * business person.
     *
     * @var string
     */
    public $duns;

    /**
     * Event that this person is a performer or participant in.
     *
     * @var Event
     */
    public $performerIn;

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
     * An honorific prefix preceding a Person's name such as Dr/Mrs/Mr.
     *
     * @var string
     */
    public $honorificPrefix;

    /**
     * A parents of the person.
     *
     * @var Person
     */
    public $parents;

    /**
     * Family name. In the U.S., the last name of a Person.
     *
     * @var string
     */
    public $familyName;

    /**
     * A sibling of the person.
     *
     * @var Person
     */
    public $siblings;

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
     * The Person's occupation. For past professions, use Role for expressing
     * dates.
     *
     * @var Occupation
     */
    public $hasOccupation;

    /**
     * The total financial value of the person as calculated by subtracting assets
     * from liabilities.
     *
     * @var MonetaryAmount|PriceSpecification
     */
    public $netWorth;

    /**
     * A contact point for a person or organization.
     *
     * @var ContactPoint
     */
    public $contactPoint;

    /**
     * A contact location for a person's residence.
     *
     * @var ContactPoint|Place
     */
    public $homeLocation;

    /**
     * Gender of something, typically a [[Person]], but possibly also fictional
     * characters, animals, etc. While http://schema.org/Male and
     * http://schema.org/Female may be used, text strings are also acceptable for
     * people who do not identify as a binary gender. The [[gender]] property can
     * also be used in an extended sense to cover e.g. the gender of sports teams.
     * As with the gender of individuals, we do not try to enumerate all
     * possibilities. A mixed-gender [[SportsTeam]] can be indicated with a text
     * value of "Mixed".
     *
     * @var GenderType|string
     */
    public $gender;

    /**
     * Indicates an OfferCatalog listing for this Organization, Person, or
     * Service.
     *
     * @var OfferCatalog
     */
    public $hasOfferCatalog;

    /**
     * The most generic uni-directional social relation.
     *
     * @var Person
     */
    public $follows;

    /**
     * The place where the person was born.
     *
     * @var Place
     */
    public $birthPlace;

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
     * A [callsign](https://en.wikipedia.org/wiki/Call_sign), as used in
     * broadcasting and radio communications to identify people, radio and TV
     * stations, or vehicles.
     *
     * @var string
     */
    public $callSign;

    /**
     * The North American Industry Classification System (NAICS) code for a
     * particular organization or business person.
     *
     * @var string
     */
    public $naics;

    /**
     * The place where the person died.
     *
     * @var Place
     */
    public $deathPlace;

    /**
     * An organization that the person is an alumni of.
     *
     * @var Organization|EducationalOrganization
     */
    public $alumniOf;

    /**
     * A colleague of the person.
     *
     * @var Person
     */
    public $colleagues;

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
