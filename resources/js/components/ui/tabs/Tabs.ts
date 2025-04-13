import { cn } from "@/lib/utils"
import { computed, defineComponent, h, inject, provide, ref, type InjectionKey, type Ref } from "vue"

interface TabsContextValue {
  value: Ref<string | undefined>
  setValue: (value: string) => void
  orientation: Ref<string>
  activationMode: Ref<string>
}

const TabsContext = Symbol("TabsContext") as InjectionKey<TabsContextValue>

export const Tabs = defineComponent({
  name: "Tabs",
  props: {
    defaultValue: {
      type: String,
      required: false,
    },
    value: {
      type: String,
      required: false,
    },
    orientation: {
      type: String,
      default: "horizontal",
      validator: (value: string) => ["horizontal", "vertical"].includes(value),
    },
    activationMode: {
      type: String,
      default: "automatic",
      validator: (value: string) => ["automatic", "manual"].includes(value),
    },
  },
  emits: ["update:value"],
  setup(props, { emit, slots }) {
    const valueRef = ref(props.value ?? props.defaultValue)

    const setValue = (newValue: string) => {
      if (props.value === undefined) {
        valueRef.value = newValue
      }
      emit("update:value", newValue)
    }

    provide(TabsContext, {
      value: valueRef,
      setValue,
      orientation: computed(() => props.orientation),
      activationMode: computed(() => props.activationMode),
    })

    return () => h("div", { class: "w-full" }, slots.default?.())
  },
})

export const TabsList = defineComponent({
  name: "TabsList",
  setup(_, { slots }) {
    const context = inject(TabsContext)

    return () =>
      h(
        "div",
        {
          class: cn(
            "inline-flex h-10 items-center justify-center rounded-md bg-muted p-1 text-muted-foreground",
            context?.orientation.value === "vertical" &&
              "flex-col h-auto"
          ),
          role: "tablist",
          "aria-orientation": context?.orientation.value,
        },
        slots.default?.()
      )
  },
})

export const TabsTrigger = defineComponent({
  name: "TabsTrigger",
  props: {
    value: {
      type: String,
      required: true,
    },
    disabled: {
      type: Boolean,
      default: false,
    },
    class: {
      type: String,
      required: false,
    },
    asChild: {
      type: Boolean,
      default: false,
    },
  },
  setup(props, { slots }) {
    const context = inject(TabsContext)
    const selected = computed(() => context?.value.value === props.value)

    const handleSelect = () => {
      if (props.disabled) return
      context?.setValue(props.value)
    }

    return () =>
      h(
        "button",
        {
          class: cn(
            "inline-flex items-center justify-center whitespace-nowrap rounded-sm px-3 py-1.5 text-sm font-medium ring-offset-background transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50",
            selected.value
              ? "bg-background text-foreground shadow-sm"
              : "hover:bg-muted/50 hover:text-foreground",
            context?.orientation.value === "vertical" && "justify-start",
            props.class
          ),
          role: "tab",
          "aria-selected": selected.value,
          "data-state": selected.value ? "active" : "inactive",
          disabled: props.disabled,
          onClick: handleSelect,
        },
        slots.default?.()
      )
  },
})

export const TabsContent = defineComponent({
  name: "TabsContent",
  props: {
    value: {
      type: String,
      required: true,
    },
    forceMount: {
      type: Boolean,
      default: false,
    },
    class: {
      type: String,
      required: false,
    },
    asChild: {
      type: Boolean,
      default: false,
    },
  },
  setup(props, { slots }) {
    const context = inject(TabsContext)
    const selected = computed(() => context?.value.value === props.value)

    return () => {
      if (!props.forceMount && !selected.value) return null

      return h(
        "div",
        {
          class: cn(
            "mt-2 ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2",
            props.class
          ),
          role: "tabpanel",
          hidden: !selected.value,
          "data-state": selected.value ? "active" : "inactive",
        },
        slots.default?.()
      )
    }
  },
}) 