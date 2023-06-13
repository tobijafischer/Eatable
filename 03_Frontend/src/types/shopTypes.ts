import latlonSpherical from "geodesy/latlon-spherical";

export interface SingleShop {
  id: string;
  title: string;
  slug: string;
  main_image: string;
  gallery: string | null;
  description: string | null;
  guidle_snowflake_ch_id: string | null;
  street: string; // is there always an address?
  zip: string;
  city: string;
  website: string | null;
  location_lat: string | null;
  location_lon: string | null;
  author: string;
  creation_date: string;
  active: string;

  latlon?: latlonSpherical;
  currentDistance?: number;
  distanceIndicator?: string;
}