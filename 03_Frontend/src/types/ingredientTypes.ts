export interface SingleIngredient {
  id: number;
  title: string;
  slug: string;
  main_image: string;
  partner: number;
  category: number | null;
  description: string | null;
  nutrition_value: string | number | null;
  co2_16fu_ohne_flug: number | null;
  g_co2_100g_ohne_flug: number | null;
  creation_date: string;
  active: number;
}

export interface RecipeIngredient {
  id: string;
  quantity: string;
  unit: string;
}

export interface IngredientSeason {
  id: number;
  ingredient_id: number;
  january: number;
  february: number;
  march: number;
  april: number;
  may: number;
  june: number;
  july: number;
  august: number;
  september: number;
  october: number;
  november: number;
  december: number;
  active: number;
}