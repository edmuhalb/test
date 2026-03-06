/// <reference types="react-scripts" />

/** API виджета чата 1С:Диалог (появляется в window после загрузки скрипта) */
declare global {
  interface Window {
    CollaborationSystemWebChat1CE?: {
      open(): void;
      close(): void;
      setContactInfo(contactInfo: {
        name?: string;
        fullName?: string;
        email?: string;
        phone?: string;
      }): void;
    };
  }
}
export {};
