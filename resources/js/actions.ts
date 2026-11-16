import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../wayfinder'
/**
* @see \::index
 * @see [unknown]:0
 * @route '/tracking-status'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/tracking-status',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \::index
 * @see [unknown]:0
 * @route '/tracking-status'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \::index
 * @see [unknown]:0
 * @route '/tracking-status'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \::index
 * @see [unknown]:0
 * @route '/tracking-status'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

    /**
* @see \::index
 * @see [unknown]:0
 * @route '/tracking-status'
 */
    const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: index.url(options),
        method: 'get',
    })

            /**
* @see \::index
 * @see [unknown]:0
 * @route '/tracking-status'
 */
        indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: index.url(options),
            method: 'get',
        })
            /**
* @see \::index
 * @see [unknown]:0
 * @route '/tracking-status'
 */
        indexForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: index.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    index.form = indexForm
/**
* @see \::create
 * @see [unknown]:0
 * @route '/tracking-status/create'
 */
export const create = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/tracking-status/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \::create
 * @see [unknown]:0
 * @route '/tracking-status/create'
 */
create.url = (options?: RouteQueryOptions) => {
    return create.definition.url + queryParams(options)
}

/**
* @see \::create
 * @see [unknown]:0
 * @route '/tracking-status/create'
 */
create.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})
/**
* @see \::create
 * @see [unknown]:0
 * @route '/tracking-status/create'
 */
create.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(options),
    method: 'head',
})

    /**
* @see \::create
 * @see [unknown]:0
 * @route '/tracking-status/create'
 */
    const createForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: create.url(options),
        method: 'get',
    })

            /**
* @see \::create
 * @see [unknown]:0
 * @route '/tracking-status/create'
 */
        createForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: create.url(options),
            method: 'get',
        })
            /**
* @see \::create
 * @see [unknown]:0
 * @route '/tracking-status/create'
 */
        createForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: create.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    create.form = createForm
/**
* @see \::show
 * @see [unknown]:0
 * @route '/tracking-status/{status}'
 */
export const show = (args: { status: string | number } | [status: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/tracking-status/{status}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \::show
 * @see [unknown]:0
 * @route '/tracking-status/{status}'
 */
show.url = (args: { status: string | number } | [status: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { status: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    status: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        status: args.status,
                }

    return show.definition.url
            .replace('{status}', parsedArgs.status.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \::show
 * @see [unknown]:0
 * @route '/tracking-status/{status}'
 */
show.get = (args: { status: string | number } | [status: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})
/**
* @see \::show
 * @see [unknown]:0
 * @route '/tracking-status/{status}'
 */
show.head = (args: { status: string | number } | [status: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

    /**
* @see \::show
 * @see [unknown]:0
 * @route '/tracking-status/{status}'
 */
    const showForm = (args: { status: string | number } | [status: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: show.url(args, options),
        method: 'get',
    })

            /**
* @see \::show
 * @see [unknown]:0
 * @route '/tracking-status/{status}'
 */
        showForm.get = (args: { status: string | number } | [status: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: show.url(args, options),
            method: 'get',
        })
            /**
* @see \::show
 * @see [unknown]:0
 * @route '/tracking-status/{status}'
 */
        showForm.head = (args: { status: string | number } | [status: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: show.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    show.form = showForm
/**
* @see \::edit
 * @see [unknown]:0
 * @route '/tracking-status/{status}/edit'
 */
export const edit = (args: { status: string | number } | [status: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/tracking-status/{status}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \::edit
 * @see [unknown]:0
 * @route '/tracking-status/{status}/edit'
 */
edit.url = (args: { status: string | number } | [status: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { status: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    status: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        status: args.status,
                }

    return edit.definition.url
            .replace('{status}', parsedArgs.status.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \::edit
 * @see [unknown]:0
 * @route '/tracking-status/{status}/edit'
 */
edit.get = (args: { status: string | number } | [status: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})
/**
* @see \::edit
 * @see [unknown]:0
 * @route '/tracking-status/{status}/edit'
 */
edit.head = (args: { status: string | number } | [status: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(args, options),
    method: 'head',
})

    /**
* @see \::edit
 * @see [unknown]:0
 * @route '/tracking-status/{status}/edit'
 */
    const editForm = (args: { status: string | number } | [status: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: edit.url(args, options),
        method: 'get',
    })

            /**
* @see \::edit
 * @see [unknown]:0
 * @route '/tracking-status/{status}/edit'
 */
        editForm.get = (args: { status: string | number } | [status: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: edit.url(args, options),
            method: 'get',
        })
            /**
* @see \::edit
 * @see [unknown]:0
 * @route '/tracking-status/{status}/edit'
 */
        editForm.head = (args: { status: string | number } | [status: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: edit.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    edit.form = editForm
/**
* @see \::store
 * @see [unknown]:0
 * @route '/tracking-status'
 */
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/tracking-status',
} satisfies RouteDefinition<["post"]>

/**
* @see \::store
 * @see [unknown]:0
 * @route '/tracking-status'
 */
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \::store
 * @see [unknown]:0
 * @route '/tracking-status'
 */
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

    /**
* @see \::store
 * @see [unknown]:0
 * @route '/tracking-status'
 */
    const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: store.url(options),
        method: 'post',
    })

            /**
* @see \::store
 * @see [unknown]:0
 * @route '/tracking-status'
 */
        storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: store.url(options),
            method: 'post',
        })
    
    store.form = storeForm
/**
* @see \::update
 * @see [unknown]:0
 * @route '/tracking-status/{status}'
 */
export const update = (args: { status: string | number } | [status: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/tracking-status/{status}',
} satisfies RouteDefinition<["put"]>

/**
* @see \::update
 * @see [unknown]:0
 * @route '/tracking-status/{status}'
 */
update.url = (args: { status: string | number } | [status: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { status: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    status: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        status: args.status,
                }

    return update.definition.url
            .replace('{status}', parsedArgs.status.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \::update
 * @see [unknown]:0
 * @route '/tracking-status/{status}'
 */
update.put = (args: { status: string | number } | [status: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

    /**
* @see \::update
 * @see [unknown]:0
 * @route '/tracking-status/{status}'
 */
    const updateForm = (args: { status: string | number } | [status: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: update.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'PUT',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \::update
 * @see [unknown]:0
 * @route '/tracking-status/{status}'
 */
        updateForm.put = (args: { status: string | number } | [status: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: update.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'PUT',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    update.form = updateForm
/**
* @see \::destroy
 * @see [unknown]:0
 * @route '/tracking-status/{status}'
 */
export const destroy = (args: { status: string | number } | [status: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/tracking-status/{status}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \::destroy
 * @see [unknown]:0
 * @route '/tracking-status/{status}'
 */
destroy.url = (args: { status: string | number } | [status: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { status: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    status: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        status: args.status,
                }

    return destroy.definition.url
            .replace('{status}', parsedArgs.status.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \::destroy
 * @see [unknown]:0
 * @route '/tracking-status/{status}'
 */
destroy.delete = (args: { status: string | number } | [status: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

    /**
* @see \::destroy
 * @see [unknown]:0
 * @route '/tracking-status/{status}'
 */
    const destroyForm = (args: { status: string | number } | [status: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: destroy.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'DELETE',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \::destroy
 * @see [unknown]:0
 * @route '/tracking-status/{status}'
 */
        destroyForm.delete = (args: { status: string | number } | [status: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: destroy.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'DELETE',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    destroy.form = destroyForm
const  = { index, create, show, edit, store, update, destroy }

export default