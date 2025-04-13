// Komponen tab
import { default as TabsComponentVue } from './Tabs.vue';
import TabsImplementation from './tabs-implementation';

// Mengekspos komponen dan definisi yang dibutuhkan
export const Tabs = TabsImplementation.Tabs;
export const TabsList = TabsImplementation.TabsList;
export const TabsTrigger = TabsImplementation.TabsTrigger;
export const TabsContent = TabsImplementation.TabsContent; 